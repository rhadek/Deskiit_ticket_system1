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
        $query = TicketRequest::where('id_custuser', $user->id)
            ->with(['projectItem.project']);

        if ($request->has('state') && $request->state != '') {
            $query->where('state', $request->state);
        }

        if ($request->has('kind') && $request->kind != '') {
            $query->where('kind', $request->kind);
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('customer.requests.index', compact('requests'));
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

    public function show(TicketRequest $request)
    {
        $user = Auth::guard('customer')->user();

        // Kontrola, zda požadavek patří aktuálnímu uživateli
        if ($request->id_custuser !== $user->id) {
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

    public function addMessage(Request $request, TicketRequest $ticketRequest)
    {
        $user = Auth::guard('customer')->user();

        // Kontrola, zda požadavek patří aktuálnímu uživateli
        if ($ticketRequest->id_custuser !== $user->id) {
            abort(403, 'Neautorizovaný přístup.');
        }

        $validated = $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        RequestMessage::create([
            'id_request' => $ticketRequest->id,
            'id_custuser' => $user->id,
            'inserted' => now(),
            'state' => 1,
            'kind' => 1,
            'message' => $validated['message'],
        ]);

        return back()->with('success', 'Zpráva byla úspěšně přidána.');
    }
}
