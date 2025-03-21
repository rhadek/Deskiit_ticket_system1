<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;
use App\Models\Request as TicketRequest;
use App\Models\ProjectItem;
use App\Models\User;

class UserDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();

        // Metriky pro dashboard
        if ($user->kind == 3) {
            // Pro admina - statistiky všech zaměstnanců
            $metrics = [
                'total_requests' => TicketRequest::whereHas('projectItem', function ($query) {
                    $query->whereHas('users');
                })->count(),
                'open_requests' => TicketRequest::whereHas('projectItem', function ($query) {
                    $query->whereHas('users');
                })->whereIn('state', [1, 2, 3])->count(),
                'resolved_requests' => TicketRequest::whereHas('projectItem', function ($query) {
                    $query->whereHas('users');
                })->whereIn('state', [4, 5])->count(),
            ];

            // Projektové položky všech zaměstnanců
            $project_items = ProjectItem::whereHas('users')
                ->with('project.customer', 'users')
                ->limit(5)
                ->get();

            // Nejnovější požadavky všech zaměstnanců
            $recent_requests = TicketRequest::whereHas('projectItem', function ($query) {
                $query->whereHas('users');
            })
                ->with(['projectItem.project.customer', 'customerUser'])
                ->orderBy('inserted', 'desc')
                ->limit(5)
                ->get();
        } else {
            // Pro běžného uživatele
            $metrics = [
                'total_requests' => TicketRequest::whereHas('projectItem', function ($query) use ($user) {
                    $query->whereHas('users', function ($q) use ($user) {
                        $q->where('users.id', $user->id);
                    });
                })->count(),
                'open_requests' => TicketRequest::whereHas('projectItem', function ($query) use ($user) {
                    $query->whereHas('users', function ($q) use ($user) {
                        $q->where('users.id', $user->id);
                    });
                })->whereIn('state', [1, 2, 3])->count(),
                'resolved_requests' => TicketRequest::whereHas('projectItem', function ($query) use ($user) {
                    $query->whereHas('users', function ($q) use ($user) {
                        $q->where('users.id', $user->id);
                    });
                })->whereIn('state', [4, 5])->count(),
            ];

            // Projektové položky přiřazené uživateli
            $project_items = $user->projectItems()
                ->with('project.customer')
                ->limit(5)
                ->get();

            // Nejnovější požadavky uživatele
            $recent_requests = TicketRequest::whereHas('projectItem', function ($query) use ($user) {
                $query->whereHas('users', function ($q) use ($user) {
                    $q->where('users.id', $user->id);
                });
            })
                ->with(['projectItem.project.customer', 'customerUser'])
                ->orderBy('inserted', 'desc')
                ->limit(5)
                ->get();
        }

        return view('dashboard', compact('metrics', 'project_items', 'recent_requests'));
    }
}
