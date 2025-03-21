<?php

namespace App\Http\Controllers;

use App\Models\RequestReport;
use App\Models\Request as TicketRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class RequestReportController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
        // No admin-only restriction anymore - allow all authenticated users
    }

    /**
     * Show the form for creating a new report.
     */
    public function create(Request $request): View
    {
        $ticketRequest = null;

        // If id_request parameter is provided, pre-fill the request
        if ($request->has('id_request')) {
            $ticketRequest = TicketRequest::with(['projectItem.project.customer', 'customerUser'])
                ->findOrFail($request->id_request);

            // Check if the user has access to this request
            $hasAccess = Auth::user()->kind == 3 || // Admin
                         Auth::user()->projectItems()
                            ->where('project_items.id', $ticketRequest->id_projectitem)
                            ->exists();

            if (!$hasAccess) {
                return redirect()->route('dashboard')
                    ->with('error', 'Nemáte přístup k tomuto požadavku.');
            }
        }

        return view('request_reports.create', compact('ticketRequest'));
    }

    /**
     * Store a newly created report.
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

        // Get the ticket request to check permissions
        $ticketRequest = TicketRequest::findOrFail($validated['id_request']);

        // Check if the user has access to this request
        $hasAccess = Auth::user()->kind == 3 || // Admin
                     Auth::user()->projectItems()
                        ->where('project_items.id', $ticketRequest->id_projectitem)
                        ->exists();

        if (!$hasAccess) {
            return redirect()->route('dashboard')
                ->with('error', 'Nemáte přístup k tomuto požadavku.');
        }

        // Add user ID and timestamp
        $validated['id_user'] = Auth::id();
        $validated['inserted'] = now();

        $report = RequestReport::create($validated);

        return redirect()->route('requests.show', $validated['id_request'])
            ->with('success', 'Report práce byl úspěšně vytvořen.');
    }

    /**
     * Display the specified report.
     */
    public function show(RequestReport $requestReport): View
    {
        $requestReport->load(['request.projectItem.project.customer', 'request.customerUser', 'user']);

        // Check if the user has access to this report
        $hasAccess = Auth::user()->kind == 3 || // Admin
                     Auth::user()->projectItems()
                        ->where('project_items.id', $requestReport->request->id_projectitem)
                        ->exists();

        if (!$hasAccess) {
            abort(403, 'Nemáte přístup k tomuto reportu.');
        }

        return view('request_reports.show', compact('requestReport'));
    }

    /**
     * Show the form for editing the specified report.
     */
    public function edit(RequestReport $requestReport): View
    {
        $requestReport->load(['request.projectItem.project.customer', 'request.customerUser']);

        // Only admins or the report creator can edit it
        $canEdit = Auth::user()->kind == 3 || // Admin
                  Auth::id() == $requestReport->id_user; // Creator

        if (!$canEdit) {
            abort(403, 'Nemáte oprávnění upravovat tento report.');
        }

        return view('request_reports.edit', compact('requestReport'));
    }

    /**
     * Update the specified report.
     */
    public function update(Request $request, RequestReport $requestReport): RedirectResponse
    {
        // Only admins or the report creator can update it
        $canEdit = Auth::user()->kind == 3 || // Admin
                  Auth::id() == $requestReport->id_user; // Creator

        if (!$canEdit) {
            abort(403, 'Nemáte oprávnění upravovat tento report.');
        }

        $validated = $request->validate([
            'work_start' => 'required|date',
            'work_end' => 'required|date|after:work_start',
            'work_total' => 'required|integer|min:1',
            'descript' => 'required|string|max:1000',
            'state' => 'required|integer',
            'kind' => 'required|integer',
        ]);

        $requestReport->update($validated);

        // Redirect back to the request detail page
        return redirect()->route('requests.show', $requestReport->id_request)
            ->with('success', 'Report práce byl úspěšně aktualizován.');
    }

    /**
     * Remove the specified report.
     */
    public function destroy(RequestReport $requestReport): RedirectResponse
    {
        // Only admins or the report creator can delete it
        $canDelete = Auth::user()->kind == 3 || // Admin
                    Auth::id() == $requestReport->id_user; // Creator

        if (!$canDelete) {
            abort(403, 'Nemáte oprávnění smazat tento report.');
        }

        // Store request ID for redirection
        $requestId = $requestReport->id_request;

        $requestReport->delete();

        return redirect()->route('requests.show', $requestId)
            ->with('success', 'Report práce byl úspěšně smazán.');
    }
}
