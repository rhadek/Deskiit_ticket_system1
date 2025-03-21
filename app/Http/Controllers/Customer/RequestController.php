<?php

namespace App\Http\Controllers\Customer;

use Illuminate\Routing\Controller;
use App\Models\Request as TicketRequest;
use App\Models\RequestMessage;
use App\Models\ProjectItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:customer');
    }

    public function index(Request $request)
    {
        $user = Auth::guard('customer')->user();

        // Filtry
        if ($user->kind == 3) {
            // Pro adminy zobrazíme všechny požadavky firmy
            $query = TicketRequest::whereHas('projectItem.project', function($q) use ($user) {
                $q->where('id_customer', $user->id_customer);
            })->with(['projectItem.project']);
        } else {
            // Pro běžné uživatele jen jejich požadavky
            $query = TicketRequest::where('id_custuser', $user->id)
                ->with(['projectItem.project']);
        }

        if ($request->has('state') && $request->state != '') {
            $query->where('state', $request->state);
        }

        if ($request->has('kind') && $request->kind != '') {
            $query->where('kind', $request->kind);
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('customer.requests.index', compact('requests'));
    }

    public function show(TicketRequest $request)
    {
        $user = Auth::guard('customer')->user();

        // Načteme související data pro kontrolu přístupu
        $request->load('projectItem.project');

        // Kontrola, zda požadavek patří ke stejné firmě jako uživatel
        if ($request->projectItem->project->id_customer !== $user->id_customer) {
            abort(403, 'Neautorizovaný přístup.');
        }

        // Pro běžné uživatele kontrolujeme, zda je to jejich požadavek
        if ($user->kind != 3 && $request->id_custuser !== $user->id) {
            abort(403, 'Neautorizovaný přístup.');
        }

        $request->load([
            'projectItem.project.customer',
            'messages' => function ($query) {
                $query->orderBy('inserted', 'asc');
            },
            'messages.user',
            'messages.customerUser',
        ]);

        return view('customer.requests.show', compact('request'));
    }

    public function create(Request $request, $id_projectitem = null)
    {
        $user = Auth::guard('customer')->user();

        // Získáme pouze projektové položky, ke kterým má uživatel přístup
        $query = $user->projectItems()
            ->where('project_items.state', 1)
            ->with('project');

        // Pokud máme specifikovanou položku, zkontrolujeme přístup
        $selectedProjectItem = null;
        if ($id_projectitem) {
            $selectedProjectItem = ProjectItem::find($id_projectitem);

            // Ověříme, že uživatel má přístup k této položce
            if (!$selectedProjectItem || !$user->projectItems()->where('project_items.id', $id_projectitem)->exists()) {
                return redirect()->route('customer.requests.create')
                    ->with('error', 'Nemáte přístup k vybrané projektové položce.');
            }
        }

        $projectItems = $query->get();

        return view('customer.requests.create', compact('projectItems', 'selectedProjectItem'));
    }

    public function store(Request $request)
    {
        $user = Auth::guard('customer')->user();

        $validated = $request->validate([
            'id_projectitem' => [
                'required',
                'exists:project_items,id',
                function ($attribute, $value, $fail) use ($user) {
                    // Ověření, že uživatel má přístup k této projektové položce
                    $hasAccess = $user->projectItems()->where('project_items.id', $value)->exists();
                    if (!$hasAccess) {
                        $fail('Nemáte přístup k této projektové položce.');
                    }
                }
            ],
            'name' => 'required|string|max:100',
            'description' => 'required|string|max:1000',
            'kind' => 'required|integer|in:1,2,3',
        ]);

        // Přidáme standardní hodnoty
        $validated['id_custuser'] = $user->id;
        $validated['inserted'] = now();
        $validated['state'] = 1; // Nový požadavek

        $ticketRequest = TicketRequest::create($validated);

        // Vytvoříme první zprávu
        RequestMessage::create([
            'id_request' => $ticketRequest->id,
            'id_custuser' => $user->id,
            'inserted' => now(),
            'state' => 1,
            'kind' => 1,
            'message' => $validated['description'],
        ]);

        return redirect()->route('customer.requests.show', $ticketRequest)
            ->with('success', 'Požadavek byl úspěšně vytvořen.');
    }



    /**
     * Přidání zprávy k požadavku.
     */
    public function addMessage(Request $request, $id)
    {
        $user = Auth::guard('customer')->user();

        // Načtení požadavku podle ID - použijeme model TicketRequest, což je přejmenovaný model Request
        $ticketRequest = TicketRequest::findOrFail($id);

        // Kontrola, zda požadavek patří aktuálnímu uživateli
        if ($ticketRequest->id_custuser !== $user->id) {
            abort(403, 'Neautorizovaný přístup.');
        }

        // Kontrola, zda požadavek není uzavřen
        if ($ticketRequest->state == 5) { // 5 = Uzavřeno
            return back()->with('error', 'Nelze přidat zprávu k uzavřenému požadavku.');
        }

        $validated = $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        // Vytvoříme novou zprávu
        RequestMessage::create([
            'id_request' => $ticketRequest->id,
            'id_custuser' => $user->id,
            'id_user' => null, // Jedná se o zprávu od zákazníka, ne od zaměstnance
            'inserted' => now(),
            'state' => 1, // Aktivní
            'kind' => 1, // Standardní typ zprávy
            'message' => $validated['message'],
        ]);

        // Pokud byl požadavek v některých stavech jako "Vyřešeno" nebo "Čeká na zpětnou vazbu",
        // můžeme ho automaticky přepnout zpět na "V řešení"
        if ($ticketRequest->state == 4 || $ticketRequest->state == 3) {  // 4 = Vyřešeno, 3 = Čeká na zpětnou vazbu
            $ticketRequest->state = 2;  // 2 = V řešení
            $ticketRequest->save();
        }

        return redirect()->route('customer.requests.show', $ticketRequest->id)
            ->with('success', 'Zpráva byla úspěšně přidána.');
    }

    /**
     * Potvrzení vyřešení požadavku a jeho uzavření zákazníkem.
     */
    public function confirmResolution(Request $request, TicketRequest $ticketRequest)
    {
        $user = Auth::guard('customer')->user();

        // Kontrola, zda požadavek patří aktuálnímu uživateli
        if ($ticketRequest->id_custuser !== $user->id) {
            abort(403, 'Neautorizovaný přístup.');
        }

        // Kontrola, zda je požadavek ve stavu "Vyřešeno"
        if ($ticketRequest->state != 4) { // 4 = Vyřešeno
            return back()->with('error', 'Požadavek musí být ve stavu "Vyřešeno", aby mohl být uzavřen.');
        }

        // Změna stavu požadavku na "Uzavřeno"
        $ticketRequest->state = 5; // 5 = Uzavřeno
        $ticketRequest->save();

        // Vytvoříme zprávu o uzavření
        RequestMessage::create([
            'id_request' => $ticketRequest->id,
            'id_custuser' => $user->id,
            'inserted' => now(),
            'state' => 1,
            'kind' => 1,
            'message' => 'Požadavek byl potvrzen jako vyřešený a uzavřen.',
        ]);

        return redirect()->route('customer.requests.show', $ticketRequest)
            ->with('success', 'Požadavek byl úspěšně uzavřen.');
    }
}
