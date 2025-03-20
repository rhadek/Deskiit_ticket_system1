<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Request as TicketRequest;
use App\Models\RequestMessage;
use App\Models\ProjectItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RequestController extends Controller
{
    public function index()
    {
        $user = Auth::guard('customer')->user();

        $requests = TicketRequest::where('id_custuser', $user->id)
            ->with(['projectItem.project', 'messages'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('customer.requests.index', compact('requests'));
    }

    public function create()
    {
        $user = Auth::guard('customer')->user();

        // Získáme pouze projektové položky, ke kterým má uživatel přístup
        $projectItems = $user->projectItems()
            ->where('project_items.state', 1)
            ->with('project')
            ->get();

        return view('customer.requests.create', compact('projectItems'));
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
        ]);

        // Přidáme standardní hodnoty
        $validated['id_custuser'] = $user->id;
        $validated['inserted'] = now();
        $validated['state'] = 1; // Nový požadavek
        $validated['kind'] = 1; // Standardní typ

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
            'projectItem.project',
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
