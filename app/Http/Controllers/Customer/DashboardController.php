<?php

namespace App\Http\Controllers\Customer;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Request as TicketRequest;
use App\Models\ProjectItem;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:customer');
    }

    public function index()
    {
        $user = Auth::guard('customer')->user();

        if ($user->kind == 3) {
            $metrics = [
                'total_requests' => TicketRequest::whereHas('projectItem.project', function ($q) use ($user) {
                    $q->where('id_customer', $user->id_customer);
                })->count(),
                'open_requests' => TicketRequest::whereHas('projectItem.project', function ($q) use ($user) {
                    $q->where('id_customer', $user->id_customer);
                })->whereIn('state', [1, 2, 3])->count(),
                'resolved_requests' => TicketRequest::whereHas('projectItem.project', function ($q) use ($user) {
                    $q->where('id_customer', $user->id_customer);
                })->whereIn('state', [4, 5])->count(),
            ];

            $recent_requests = TicketRequest::whereHas('projectItem.project', function ($q) use ($user) {
                $q->where('id_customer', $user->id_customer);
            })
                ->with('projectItem.project')
                ->orderBy('inserted', 'desc')
                ->limit(5)
                ->get();

            $project_items = ProjectItem::whereHas('project', function ($q) use ($user) {
                $q->where('id_customer', $user->id_customer);
            })
                ->with('project')
                ->limit(5)
                ->get();
        } else {
            $metrics = [
                'total_requests' => TicketRequest::where('id_custuser', $user->id)->count(),
                'open_requests' => TicketRequest::where('id_custuser', $user->id)
                    ->whereIn('state', [1, 2, 3])
                    ->count(),
                'resolved_requests' => TicketRequest::where('id_custuser', $user->id)
                    ->whereIn('state', [4, 5])
                    ->count(),
            ];

            $recent_requests = TicketRequest::where('id_custuser', $user->id)
                ->with('projectItem.project')
                ->orderBy('inserted', 'desc')
                ->limit(5)
                ->get();

            $project_items = $user->projectItems()
                ->with('project')
                ->limit(5)
                ->get();
        }

        return view('customer.dashboard', compact('metrics', 'recent_requests', 'project_items'));
    }
}
