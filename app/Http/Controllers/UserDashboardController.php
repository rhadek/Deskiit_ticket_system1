<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;
use App\Models\Request as TicketRequest;
use App\Models\ProjectItem;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

class UserDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $selectedProjectId = $request->query('project_id');

        if ($user->kind == 3) {
            $metrics = [
                'total_requests' => TicketRequest::count(),
                'open_requests' => TicketRequest::whereIn('state', [1, 2, 3])->count(),
                'resolved_requests' => TicketRequest::whereIn('state', [4, 5])->count(),
            ];

            $projects = Project::with('customer')
                ->orderBy('name')
                ->get();

            $query = ProjectItem::with(['project.customer']);

            if ($selectedProjectId) {
                $query->where('id_project', $selectedProjectId);
            }

            $project_items = $query->get();

            $recent_requests = TicketRequest::with(['projectItem.project.customer', 'customerUser'])
                ->orderBy('inserted', 'desc')
                ->limit(10)
                ->get();
        } else {
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

            $projectIds = $user->projectItems()
                ->join('projects', 'project_items.id_project', '=', 'projects.id')
                ->distinct()
                ->pluck('projects.id');

            $projects = Project::whereIn('id', $projectIds)
                ->with('customer')
                ->orderBy('name')
                ->get();

            $query = $user->projectItems()
                ->with(['project.customer']);

            if ($selectedProjectId) {
                $query->where('project_items.id_project', $selectedProjectId);
            }

            $project_items = $query->get();

            $recent_requests = TicketRequest::whereHas('projectItem', function ($query) use ($user) {
                $query->whereHas('users', function ($q) use ($user) {
                    $q->where('users.id', $user->id);
                });
            })
                ->with(['projectItem.project.customer', 'customerUser'])
                ->orderBy('inserted', 'desc')
                ->limit(10)
                ->get();
        }

        $grouped_by_state = $project_items->groupBy('state');
        $grouped_by_kind = $project_items->groupBy('kind');

        $state_names = [
            1 => 'Nový',
            2 => 'V řešení',
            3 => 'Čeká na zpětnou vazbu',
            4 => 'Vyřešeno',
            5 => 'Uzavřeno',
        ];

        $kind_names = [
            1 => 'Standardní',
            2 => 'Systémová',
            3 => 'Prioritní',
        ];

        $selectedProject = $selectedProjectId ? Project::find($selectedProjectId) : null;

        return view('dashboard', compact(
            'metrics',
            'projects',
            'project_items',
            'recent_requests',
            'grouped_by_state',
            'grouped_by_kind',
            'state_names',
            'kind_names',
            'selectedProject',
            'selectedProjectId'
        ));
    }
}
