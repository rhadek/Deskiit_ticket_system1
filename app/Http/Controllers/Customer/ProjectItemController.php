<?php

namespace App\Http\Controllers\Customer;

use App\Models\Project;
use App\Models\ProjectItem;
use App\Models\CustomerUser;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class ProjectItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:customer');
    }

    public function show(ProjectItem $projectItem)
    {
        $user = Auth::guard('customer')->user();

        $projectItem->load('project');

        if ($projectItem->project->id_customer !== $user->id_customer) {
            abort(403, 'Neautorizovaný přístup.');
        }

        if ($user->kind != 3 && !$user->projectItems()->where('project_items.id', $projectItem->id)->exists()) {
            abort(403, 'Nemáte oprávnění zobrazit tuto projektovou položku.');
        }

        if ($user->kind == 3) {
            $projectItem->load([
                'project.customer',
                'requests' => function ($query) use ($user, $projectItem) {
                    $query->where('id_projectitem', $projectItem->id)
                        ->orderBy('created_at', 'desc')
                        ->limit(10);
                }
            ]);
        } else {
            $projectItem->load([
                'project.customer',
                'requests' => function ($query) use ($user) {
                    $query->where('id_custuser', $user->id)
                        ->orderBy('created_at', 'desc')
                        ->limit(10);
                }
            ]);
        }

        return view('customer.project_items.show', compact('projectItem'));
    }

    public function create(Request $request)
    {
        $user = Auth::guard('customer')->user();
        if ($user->kind != 3) {
            abort(403, 'Nemáte oprávnění vytvářet projektové položky.');
        }

        $selectedProject = null;

        if ($request->has('id_project')) {
            $selectedProject = Project::findOrFail($request->id_project);
            if ($selectedProject->id_customer != $user->id_customer) {
                abort(403, 'Nemáte oprávnění k tomuto projektu.');
            }
        }

        $projects = Project::where('id_customer', $user->id_customer)
            ->where('state', 1)
            ->get();

        return view('customer.project_items.create', compact('projects', 'selectedProject'));
    }

    public function store(Request $request)
    {
        $user = Auth::guard('customer')->user();
        if ($user->kind != 3) {
            abort(403, 'Nemáte oprávnění vytvářet projektové položky.');
        }

        $validated = $request->validate([
            'id_project' => 'required|exists:projects,id',
            'name' => 'required|string|max:100',
            'state' => 'required|integer|in:0,1',
            'kind' => 'required|integer|in:1,2,3',
        ]);

        $project = Project::findOrFail($validated['id_project']);
        if ($project->id_customer != $user->id_customer) {
            abort(403, 'Nemáte oprávnění k tomuto projektu.');
        }

        $projectItem = ProjectItem::create($validated);

        $projectItem->customerUsers()->attach($user->id);

        if ($request->has('redirect_to_project') && $request->redirect_to_project) {
            return redirect()->route('customer.projects.show', $project)
                ->with('success', 'Položka byla úspěšně vytvořena.');
        }

        return redirect()->route('customer.project_items.show', $projectItem)
            ->with('success', 'Položka byla úspěšně vytvořena.');
    }

    public function edit(ProjectItem $projectItem)
    {
        $user = Auth::guard('customer')->user();
        if ($user->kind != 3) {
            abort(403, 'Nemáte oprávnění upravovat projektové položky.');
        }

        $projectItem->load('project');

        if ($projectItem->project->id_customer != $user->id_customer) {
            abort(403, 'Nemáte oprávnění k této projektové položce.');
        }

        $projects = Project::where('id_customer', $user->id_customer)
            ->where('state', 1)
            ->get();

        return view('customer.project_items.edit', compact('projectItem', 'projects'));
    }

    public function update(Request $request, ProjectItem $projectItem)
    {
        $user = Auth::guard('customer')->user();
        if ($user->kind != 3) {
            abort(403, 'Nemáte oprávnění upravovat projektové položky.');
        }

        $projectItem->load('project');

        if ($projectItem->project->id_customer != $user->id_customer) {
            abort(403, 'Nemáte oprávnění k této projektové položce.');
        }

        $validated = $request->validate([
            'id_project' => 'required|exists:projects,id',
            'name' => 'required|string|max:100',
            'state' => 'required|integer|in:0,1',
            'kind' => 'required|integer|in:1,2,3',
        ]);

        $project = Project::findOrFail($validated['id_project']);
        if ($project->id_customer != $user->id_customer) {
            abort(403, 'Nemáte oprávnění k tomuto projektu.');
        }

        $projectItem->update($validated);

        return redirect()->route('customer.project_items.show', $projectItem)
            ->with('success', 'Položka byla úspěšně aktualizována.');
    }

    public function destroy(ProjectItem $projectItem)
    {
        $user = Auth::guard('customer')->user();
        if ($user->kind != 3) {
            abort(403, 'Nemáte oprávnění mazat projektové položky.');
        }

        $projectItem->load('project');

        if ($projectItem->project->id_customer != $user->id_customer) {
            abort(403, 'Nemáte oprávnění k této projektové položce.');
        }

        if ($projectItem->requests()->count() > 0) {
            return back()->with('error', 'Položku nelze smazat, protože obsahuje požadavky.');
        }

        $projectId = $projectItem->id_project;

        $projectItem->customerUsers()->detach();

        $projectItem->delete();

        return redirect()->route('customer.projects.show', $projectId)
            ->with('success', 'Položka byla úspěšně smazána.');
    }

    public function assignUsers(ProjectItem $projectItem)
    {
        $user = Auth::guard('customer')->user();
        if ($user->kind != 3) {
            abort(403, 'Nemáte oprávnění spravovat přístupy k projektovým položkám.');
        }

        $projectItem->load(['project', 'customerUsers']);

        if ($projectItem->project->id_customer != $user->id_customer) {
            abort(403, 'Nemáte oprávnění k této projektové položce.');
        }

        $customerUsers = CustomerUser::where('id_customer', $user->id_customer)
            ->where('state', 1)
            ->get();

        return view('customer.project_items.assign_users', compact('projectItem', 'customerUsers'));
    }

    public function storeAssignments(Request $request, ProjectItem $projectItem)
    {
        $user = Auth::guard('customer')->user();
        if ($user->kind != 3) {
            abort(403, 'Nemáte oprávnění spravovat přístupy k projektovým položkám.');
        }

        $projectItem->load('project');

        if ($projectItem->project->id_customer != $user->id_customer) {
            abort(403, 'Nemáte oprávnění k této projektové položce.');
        }

        $validated = $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:customer_users,id',
        ]);

        $count = CustomerUser::whereIn('id', $validated['user_ids'])
            ->where('id_customer', $user->id_customer)
            ->count();

        if ($count != count($validated['user_ids'])) {
            abort(403, 'Některý z vybraných uživatelů nepatří do vaší firmy.');
        }

        $projectItem->customerUsers()->sync($validated['user_ids']);

        return redirect()->route('customer.project_items.show', $projectItem)
            ->with('success', 'Přístupy byly úspěšně aktualizovány.');
    }
}
