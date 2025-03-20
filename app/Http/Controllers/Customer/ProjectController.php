<?php

namespace App\Http\Controllers\Customer;

use Illuminate\Routing\Controller;
use App\Models\Project;
use App\Models\ProjectItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:customer');
    }

    public function index()
    {
        $user = Auth::guard('customer')->user();

        // Získáme projekty zákazníka, ke kterým má uživatel přístup
        $projectIds = $user->projectItems()
            ->join('projects', 'project_items.id_project', '=', 'projects.id')
            ->where('projects.id_customer', $user->id_customer)
            ->distinct()
            ->pluck('projects.id');

        $projects = Project::whereIn('id', $projectIds)
            ->where('id_customer', $user->id_customer)
            ->with('customer')
            ->paginate(10);

        return view('customer.projects.index', compact('projects'));
    }

    public function show(Project $project)
    {
        $user = Auth::guard('customer')->user();

        // Kontrola, zda projekt patří ke stejnému zákazníkovi jako uživatel
        if ($project->id_customer !== $user->id_customer) {
            abort(403, 'Neautorizovaný přístup.');
        }

        // Načteme pouze projektové položky, ke kterým má uživatel přístup
        $projectItems = $user->projectItems()
            ->where('id_project', $project->id)
            ->paginate(10);

        // Načteme kompletní projekt včetně zákazníka
        $project->load('customer');

        return view('customer.projects.show', compact('project', 'projectItems'));
    }
}
