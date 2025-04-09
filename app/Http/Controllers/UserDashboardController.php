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
    $selectedProjectItemId = $request->query('project_item_id');

    if ($user->kind == 3) {
        $metrics = [
            'total_requests' => TicketRequest::count(),
            'open_requests' => TicketRequest::whereIn('state', [1, 2, 3])->count(),
            'resolved_requests' => TicketRequest::whereIn('state', [4, 5])->count(),
        ];

        $projects = Project::with('customer')
            ->orderBy('name')
            ->get();

        // Načteme projektové položky včetně jejich požadavků
        $query = ProjectItem::with(['project.customer', 'requests.customerUser']);

        if ($selectedProjectId) {
            $query->where('id_project', $selectedProjectId);
        }

        $project_items = $query->get();

        // Načítání požadavků podle vybraných filtrů
        if ($selectedProjectItemId) {
            // Pokud je vybrána projektová položka
            $requests = TicketRequest::where('id_projectitem', $selectedProjectItemId)
                ->with(['projectItem.project.customer', 'customerUser'])
                ->orderBy('inserted', 'desc')
                ->get();
        } elseif ($selectedProjectId) {
            // Pokud je vybrán projekt, zobrazíme požadavky všech jeho položek
            $projectItemIds = $project_items->pluck('id')->toArray();
            $requests = TicketRequest::whereIn('id_projectitem', $projectItemIds)
                ->with(['projectItem.project.customer', 'customerUser'])
                ->orderBy('inserted', 'desc')
                ->get();
        } else {
            // Pokud není nic vybráno, zobrazíme všechny požadavky (s limitem pro výkon)
            $requests = TicketRequest::with(['projectItem.project.customer', 'customerUser'])
                ->orderBy('inserted', 'desc')
                ->limit(50)
                ->get();
        }

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

        // Načteme projektové položky včetně jejich požadavků pro běžné uživatele
        $query = $user->projectItems()
            ->with(['project.customer', 'requests.customerUser']);

        if ($selectedProjectId) {
            $query->where('project_items.id_project', $selectedProjectId);
        }

        $project_items = $query->get();

        // Načítání požadavků podle vybraných filtrů pro běžné uživatele
        $requestsQuery = TicketRequest::whereHas('projectItem', function ($query) use ($user) {
                $query->whereHas('users', function ($q) use ($user) {
                    $q->where('users.id', $user->id);
                });
            })
            ->with(['projectItem.project.customer', 'customerUser']);

        if ($selectedProjectItemId) {
            // Pokud je vybrána projektová položka
            $requestsQuery->where('id_projectitem', $selectedProjectItemId);
        } elseif ($selectedProjectId) {
            // Pokud je vybrán projekt, zobrazíme požadavky všech jeho položek
            $projectItemIds = $project_items->pluck('id')->toArray();
            $requestsQuery->whereIn('id_projectitem', $projectItemIds);
        }

        $requests = $requestsQuery->orderBy('inserted', 'desc')
            ->when(!$selectedProjectId && !$selectedProjectItemId, function($query) {
                return $query->limit(50); // Limit pouze pokud není vybrán filtr
            })
            ->get();

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

    // Seskupíme požadavky podle stavu
    $requests_by_state = $requests->groupBy('state');

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
    $selectedProjectItem = $selectedProjectItemId ? ProjectItem::find($selectedProjectItemId) : null;

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
        'selectedProjectId',
        'selectedProjectItem',
        'selectedProjectItemId',
        'requests',
        'requests_by_state'
    ));
}
}
