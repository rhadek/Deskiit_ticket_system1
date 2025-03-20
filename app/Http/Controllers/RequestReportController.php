<?php

namespace App\Http\Controllers;

use App\Models\RequestReport;
use App\Models\Request as TicketRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Http\Middleware\IsAdmin;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class RequestReportController extends Controller
{
    /**
     * Vytvoření instance nového controlleru.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Zobrazí formulář pro vytvoření nového reportu práce.
     */
    public function create(Request $request): View
    {
        $ticketRequest = null;

        // Pokud je předán parametr id_request, předvyplníme požadavek
        if ($request->has('id_request')) {
            $ticketRequest = TicketRequest::with(['projectItem.project.customer', 'customerUser'])
                ->findOrFail($request->id_request);
        }

        return view('request_reports.create', compact('ticketRequest'));
    }

    /**
     * Uloží nový report práce do databáze.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id_request' => 'required|exists:requests,id',
            'work_start' => 'required|date',
            'work_end' => 'required|date|after:work_start',
            'work_total' => 'required|integer|min:1',
            'descript' => 'required|string|max:1000',
            'state' => 'required|integer',
            'kind' => 'required|integer',
        ]);

        // Přidáme ID uživatele a timestamp vytvoření
        $validated['id_user'] = Auth::id();
        $validated['inserted'] = now();

        $report = RequestReport::create($validated);

        return redirect()->route('requests.show', $validated['id_request'])
            ->with('success', 'Report práce byl úspěšně vytvořen.');
    }

    /**
     * Zobrazí detail konkrétního reportu práce.
     */
    public function show(RequestReport $requestReport): View
    {
        $requestReport->load(['request.projectItem.project.customer', 'request.customerUser', 'user']);

        return view('request_reports.show', compact('requestReport'));
    }

    /**
     * Zobrazí formulář pro úpravu reportu práce.
     */
    public function edit(RequestReport $requestReport): View
    {
        $requestReport->load(['request.projectItem.project.customer', 'request.customerUser']);

        return view('request_reports.edit', compact('requestReport'));
    }

    /**
     * Aktualizuje report práce v databázi.
     */
    /**
 * Aktualizuje report práce v databázi.
 */
public function update(Request $request, RequestReport $requestReport): RedirectResponse
{
    $validated = $request->validate([
        'work_start' => 'required|date',
        'work_end' => 'required|date|after:work_start',
        'work_total' => 'required|integer|min:1',
        'descript' => 'required|string|max:1000',
        'state' => 'required|integer',
        'kind' => 'required|integer',
    ]);

    $requestReport->update($validated);

    // Přesměrování zpět na detail požadavku místo detailu reportu
    return redirect()->route('requests.show', $requestReport->id_request)
        ->with('success', 'Report práce byl úspěšně aktualizován.');
}

    /**
     * Smaže report práce z databáze.
     */
    public function destroy(RequestReport $requestReport): RedirectResponse
    {
        // Uložíme si ID požadavku pro přesměrování
        $requestId = $requestReport->id_request;

        $requestReport->delete();

        return redirect()->route('requests.show', $requestId)
            ->with('success', 'Report práce byl úspěšně smazán.');
    }
}
