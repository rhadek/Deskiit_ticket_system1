<?php

namespace App\Http\Controllers;

use App\Models\Request as TicketRequest;
use App\Models\ProjectItem;
use App\Models\CustomerUser;
use App\Models\User;
use App\Models\RequestMessage;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Http\Middleware\IsAdmin;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class RequestController extends Controller
{
    /**
     * Vytvoření instance nového controlleru.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Zobrazí seznam všech požadavků.
     */
    public function index(): View
    {
        // Načtení parametrů z query stringu, např. ?state=...&kind=...
        $state = request('state');
        $kind = request('kind');

        // Základní dotaz
        $requestsQuery = TicketRequest::with(['projectItem.project.customer', 'customerUser']);

        // Rozlišení přístupových práv
        if (Auth::user()->kind != 3) {
            // Pro běžné uživatele zobrazujeme pouze požadavky na přiřazených položkách
            $projectItems = Auth::user()->projectItems()->pluck('id');
            $requestsQuery->whereIn('id_projectitem', $projectItems);
        }

        // Vlastní filtrování podle state
        if (!empty($state)) {
            $requestsQuery->where('state', $state);
        }

        // Filtrování podle kind
        if (!empty($kind)) {
            $requestsQuery->where('kind', $kind);
        }

        // Stránkování
        $requests = $requestsQuery->paginate(10);

        return view('requests.index', compact('requests'));
    }


    /**
     * Zobrazí seznam požadavků konkrétní projektové položky.
     */
    public function projectItemRequests(ProjectItem $projectItem): View
    {
        $requests = TicketRequest::where('id_projectitem', $projectItem->id)
            ->with(['customerUser'])
            ->paginate(10);

        return view('requests.project_item_requests', compact('requests', 'projectItem'));
    }

    /**
     * Zobrazí formulář pro vytvoření nového požadavku.
     */
    public function create(Request $request): View
    {
        $projectItems = [];
        $selectedProjectItem = null;

        // Pro admina zobrazujeme všechny aktivní položky
        if (Auth::user()->kind == 3) {
            $projectItems = ProjectItem::where('state', 1)
                ->with('project.customer')
                ->get();
        } else {
            // Pro běžné uživatele pouze přiřazené položky
            $projectItems = Auth::user()->projectItems()
                ->where('project_items.state', 1)
                ->with('project.customer')
                ->get();
        }

        // Pokud je předán parametr id_projectitem, předvyplníme položku
        if ($request->has('id_projectitem')) {
            $selectedProjectItem = ProjectItem::with('project.customer')->findOrFail($request->id_projectitem);
        }

        // Pro admina umožníme vybrat zákaznického uživatele, pro ostatní je to automaticky přihlášený uživatel
        $customerUsers = [];
        if (Auth::user()->kind == 3) {
            if ($selectedProjectItem) {
                // Pokud je vybrána položka, nabízíme pouze přiřazené zákaznické uživatele
                $customerUsers = $selectedProjectItem->customerUsers;
            } else {
                // Jinak všechny aktivní zákaznické uživatele
                $customerUsers = CustomerUser::where('state', 1)->get();
            }
        }

        return view('requests.create', compact('projectItems', 'selectedProjectItem', 'customerUsers'));
    }

    /**
     * Uloží nový požadavek do databáze.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id_projectitem' => 'required|exists:project_items,id',
            'id_custuser' => 'required|exists:customer_users,id',
            'name' => 'required|string|max:100',
            'description' => 'required|string|max:1000',
            'state' => 'required|integer',
            'kind' => 'required|integer',
        ]);

        // Přidáme timestamp vytvoření
        $validated['inserted'] = now();

        $ticketRequest = TicketRequest::create($validated);

        // Vytvoříme automaticky první zprávu od zákaznického uživatele
        RequestMessage::create([
            'id_request' => $ticketRequest->id,
            'id_custuser' => $validated['id_custuser'],
            'inserted' => now(),
            'state' => 1,
            'kind' => 1,
            'message' => $validated['description'],
        ]);

        // Pokud jsme přišli z detailu projektové položky, vrátíme se zpět
        if ($request->has('redirect_to_projectitem') && $request->redirect_to_projectitem) {
            return redirect()->route('project_items.show', $validated['id_projectitem'])
                ->with('success', 'Požadavek byl úspěšně vytvořen.');
        }

        return redirect()->route('requests.show', $ticketRequest)
            ->with('success', 'Požadavek byl úspěšně vytvořen.');
    }

    /**
     * Zobrazí detail konkrétního požadavku.
     */
    public function show(TicketRequest $request): View
    {
        // Načteme všechny související data včetně uživatelů zpráv
        $request->load([
            'projectItem.project.customer',
            'customerUser',
            'messages' => function ($query) {
                $query->orderBy('inserted', 'asc');
            },
            'messages.user',
            'messages.customerUser',
            'reports' => function ($query) {
                $query->orderBy('inserted', 'desc');
            },
            'reports.user'
        ]);

        // Pro jistotu zkontrolujeme, zda existují nějaké zprávy a případně doplníme chybějící relace
        if ($request->messages->isNotEmpty()) {
            foreach ($request->messages as $message) {
                // Ošetříme případ, kdy existuje id_user, ale user relace není načtena
                if ($message->id_user && !$message->relationLoaded('user')) {
                    $message->load('user');
                }

                // Ošetříme případ, kdy existuje id_custuser, ale customerUser relace není načtena
                if ($message->id_custuser && !$message->relationLoaded('customerUser')) {
                    $message->load('customerUser');
                }
            }
        }

        return view('requests.show', compact('request'));
    }

    /**
     * Zobrazí formulář pro úpravu požadavku.
     */
    public function edit(TicketRequest $request): View
    {
        $request->load(['projectItem.project.customer', 'customerUser']);

        return view('requests.edit', compact('request'));
    }

    /**
     * Aktualizuje požadavek v databázi.
     */
    public function update(Request $httpRequest, TicketRequest $request): RedirectResponse
    {
        $validated = $httpRequest->validate([
            'name' => 'required|string|max:100',
            'state' => 'required|integer',
            'kind' => 'required|integer',
        ]);

        $request->update($validated);

        return redirect()->route('requests.show', $request)
            ->with('success', 'Požadavek byl úspěšně aktualizován.');
    }

    /**
     * Změna stavu požadavku.
     */
    public function changeState(Request $httpRequest, TicketRequest $request): RedirectResponse
    {
        $validated = $httpRequest->validate([
            'state' => 'required|integer',
        ]);

        $request->update(['state' => $validated['state']]);

        return back()->with('success', 'Stav požadavku byl úspěšně změněn.');
    }

    /**
     * Přidání nové zprávy k požadavku.
     */
    public function addMessage(Request $httpRequest, TicketRequest $request): RedirectResponse
    {
        $validated = $httpRequest->validate([
            'message' => 'required|string|max:1000',
        ]);

        // Vytvoříme základní data zprávy
        $messageData = [
            'id_request' => $request->id,
            'inserted' => now(),
            'state' => 1,
            'kind' => 1,
            'message' => $validated['message'],
        ];

        // Zjistíme, zda je přihlášený uživatel zaměstnanec nebo zákaznický uživatel
        if (Auth::guard('web')->check()) {
            // Zpráva od zaměstnance
            $messageData['id_user'] = Auth::id();
        } else if (Auth::guard('customer')->check()) {
            // Zpráva od zákaznického uživatele
            $messageData['id_custuser'] = Auth::guard('customer')->id();
        }

        // Pokud z nějakého důvodu nejsme přihlášeni (nemělo by nastat), nastavíme zprávu jako admin
        if (!isset($messageData['id_user']) && !isset($messageData['id_custuser'])) {
            $messageData['id_user'] = Auth::id() ?? 1; // ID admina jako fallback
        }

        RequestMessage::create($messageData);

        return back()->with('success', 'Zpráva byla úspěšně přidána.');
    }
}
