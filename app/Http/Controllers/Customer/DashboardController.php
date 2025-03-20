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

        // Metriky pro dashboard
        $metrics = [
            'total_requests' => TicketRequest::where('id_custuser', $user->id)->count(),
            'open_requests' => TicketRequest::where('id_custuser', $user->id)
                ->whereIn('state', [1, 2, 3]) // Nový, V řešení, Čeká na zpětnou vazbu
                ->count(),
            'resolved_requests' => TicketRequest::where('id_custuser', $user->id)
                ->whereIn('state', [4, 5]) // Vyřešeno, Uzavřeno
                ->count(),
        ];

        // Poslední požadavky
        $recent_requests = TicketRequest::where('id_custuser', $user->id)
            ->with('projectItem.project')
            ->orderBy('inserted', 'desc')
            ->limit(5)
            ->get();

        // Projektové položky
        $project_items = $user->projectItems()
            ->with('project')
            ->limit(5)
            ->get();

        return view('customer.dashboard', compact('metrics', 'recent_requests', 'project_items'));
    }
}
