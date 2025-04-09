<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectItem;
use App\Models\User;
use App\Models\CustomerUser;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Http\Middleware\IsAdmin;
use Illuminate\Routing\Controller;

class ProjectItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(IsAdmin::class)->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index(): View
    {
        if (auth()->user()->kind == 3) {
            $projectItems = ProjectItem::with('project.customer')->paginate(10);
        } else {
            $projectItems = auth()->user()->projectItems()
                ->with('project.customer')
                ->paginate(10);
        }
        return view('project_items.index', compact('projectItems'));
    }

    public function projectItems(Project $project): View
    {
        $projectItems = ProjectItem::where('id_project', $project->id)->paginate(10);
        return view('project_items.project_items', compact('projectItems', 'project'));
    }

    public function create(Request $request): View
    {
        $projects = Project::where('state', 1)->with('customer')->get();
        $selectedProject = null;

        if ($request->has('id_project')) {
            $selectedProject = Project::with('customer')->findOrFail($request->id_project);
        }

        return view('project_items.create', compact('projects', 'selectedProject'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id_project' => 'required|exists:projects,id',
            'name' => 'required|string|max:100',
            'state' => 'required|integer',
            'kind' => 'required|integer',
        ]);

        $projectItem = ProjectItem::create($validated);

        if ($request->has('redirect_to_project') && $request->redirect_to_project) {
            return redirect()->route('projects.show', $validated['id_project'])
                ->with('success', 'Položka byla úspěšně vytvořena.');
        }

        return redirect()->route('project_items.index')
            ->with('success', 'Položka byla úspěšně vytvořena.');
    }

    public function show(ProjectItem $projectItem): View
    {

        $projectItem->load([
            'project.customer',
            'users',
            'customerUsers',
            'requests' => function($query) {
                $query->orderBy('created_at', 'desc')->limit(5);
            }
        ]);

        $availableUsers = User::where('state', 1)->get();
        $availableCustomerUsers = CustomerUser::where('state', 1)
            ->where('id_customer', $projectItem->project->id_customer)
            ->get();

        return view('project_items.show', compact('projectItem', 'availableUsers', 'availableCustomerUsers'));
    }

    public function edit(ProjectItem $projectItem): View
    {
        $projects = Project::where('state', 1)->with('customer')->get();
        return view('project_items.edit', compact('projectItem', 'projects'));
    }

    public function update(Request $request, ProjectItem $projectItem): RedirectResponse
    {
        $validated = $request->validate([
            'id_project' => 'required|exists:projects,id',
            'name' => 'required|string|max:100',
            'state' => 'required|integer',
            'kind' => 'required|integer',
        ]);

        $projectItem->update($validated);

        return redirect()->route('project_items.show', $projectItem)
            ->with('success', 'Položka byla úspěšně aktualizována.');
    }

    public function destroy(ProjectItem $projectItem): RedirectResponse
    {
        $projectId = $projectItem->id_project;

        if ($projectItem->requests()->count() > 0) {
            return back()
                ->with('error', 'Položku nelze smazat, protože má přiřazené požadavky.');
        }

        $projectItem->users()->detach();
        $projectItem->customerUsers()->detach();

        $projectItem->delete();

        if (url()->previous() === route('projects.show', $projectId)) {
            return redirect()->route('projects.show', $projectId)
                ->with('success', 'Položka byla úspěšně smazána.');
        }

        return redirect()->route('project_items.index')
            ->with('success', 'Položka byla úspěšně smazána.');
    }

    public function assignUser(Request $request, ProjectItem $projectItem): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        if (!$projectItem->users->contains($validated['user_id'])) {
            $projectItem->users()->attach($validated['user_id']);
            return back()->with('success', 'Uživatel byl úspěšně přiřazen.');
        }

        return back()->with('info', 'Uživatel je již přiřazen k této položce.');
    }

    public function removeUser(ProjectItem $projectItem, User $user): RedirectResponse
    {
        $projectItem->users()->detach($user->id);
        return back()->with('success', 'Uživatel byl odebrán z položky.');
    }

    public function assignCustomerUser(Request $request, ProjectItem $projectItem): RedirectResponse
    {
        $validated = $request->validate([
            'customer_user_id' => 'required|exists:customer_users,id'
        ]);

        if (!$projectItem->customerUsers->contains($validated['customer_user_id'])) {
            $projectItem->customerUsers()->attach($validated['customer_user_id']);
            return back()->with('success', 'Zákaznický uživatel byl úspěšně přiřazen.');
        }

        return back()->with('info', 'Zákaznický uživatel je již přiřazen k této položce.');
    }

    public function removeCustomerUser(ProjectItem $projectItem, CustomerUser $customerUser): RedirectResponse
    {
        $projectItem->customerUsers()->detach($customerUser->id);
        return back()->with('success', 'Zákaznický uživatel byl odebrán z položky.');
    }
}
