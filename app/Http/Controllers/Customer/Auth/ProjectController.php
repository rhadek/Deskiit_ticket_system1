<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
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

        return view('customer.projects.show', compact('project', 'projectItems'));
    }

    public function items(Project $project)
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

        return view('customer.projects.items', compact('project', 'projectItems'));
    }
}
