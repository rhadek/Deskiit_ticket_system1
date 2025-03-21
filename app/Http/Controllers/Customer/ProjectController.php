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

        if ($user->kind == 3) {
            $projects = Project::where('id_customer', $user->id_customer)
                ->with('customer')
                ->paginate(10);
        } else {
            $projectIds = $user->projectItems()
                ->join('projects', 'project_items.id_project', '=', 'projects.id')
                ->where('projects.id_customer', $user->id_customer)
                ->distinct()
                ->pluck('projects.id');

            $projects = Project::whereIn('id', $projectIds)
                ->where('id_customer', $user->id_customer)
                ->with('customer')
                ->paginate(10);
        }

        return view('customer.projects.index', compact('projects'));
    }

    public function show(Project $project)
    {
        $user = Auth::guard('customer')->user();

        if ($project->id_customer !== $user->id_customer) {
            abort(403, 'Neautorizovaný přístup.');
        }

        if ($user->kind == 3) {
            $projectItems = ProjectItem::where('id_project', $project->id)
                ->paginate(10);
        } else {
            $projectItems = $user->projectItems()
                ->where('id_project', $project->id)
                ->paginate(10);
        }

        $project->load('customer');
        return view('customer.projects.show', compact('project', 'projectItems'));
    }

    public function create()
    {
        $user = Auth::guard('customer')->user();
        if ($user->kind != 3) {
            abort(403, 'Nemáte oprávnění vytvářet projekty.');
        }

        return view('customer.projects.create', [
            'customer' => $user->customer,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::guard('customer')->user();
        if ($user->kind != 3) {
            abort(403, 'Nemáte oprávnění vytvářet projekty.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'state' => 'required|integer|in:0,1',
            'kind' => 'required|integer|in:1,2,3',
        ]);

        $validated['id_customer'] = $user->id_customer;

        $project = Project::create($validated);

        return redirect()->route('customer.projects.show', $project)
            ->with('success', 'Projekt byl úspěšně vytvořen.');
    }

    public function edit(Project $project)
    {
        $user = Auth::guard('customer')->user();
        if ($user->kind != 3 || $project->id_customer != $user->id_customer) {
            abort(403, 'Nemáte oprávnění upravovat tento projekt.');
        }

        return view('customer.projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        // Zkontrolovat, zda je uživatel admin a projekt patří jeho firmě
        $user = Auth::guard('customer')->user();
        if ($user->kind != 3 || $project->id_customer != $user->id_customer) {
            abort(403, 'Nemáte oprávnění upravovat tento projekt.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'state' => 'required|integer|in:0,1',
            'kind' => 'required|integer|in:1,2,3',
        ]);

        $project->update($validated);

        return redirect()->route('customer.projects.show', $project)
            ->with('success', 'Projekt byl úspěšně aktualizován.');
    }

    public function destroy(Project $project)
    {
        $user = Auth::guard('customer')->user();
        if ($user->kind != 3 || $project->id_customer != $user->id_customer) {
            abort(403, 'Nemáte oprávnění smazat tento projekt.');
        }

        if ($project->projectItems()->count() > 0) {
            return back()->with('error', 'Projekt nelze smazat, protože obsahuje položky.');
        }

        $project->delete();

        return redirect()->route('customer.projects.index')
            ->with('success', 'Projekt byl úspěšně smazán.');
    }
}
