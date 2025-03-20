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
    /**
     * Vytvoření instance nového controlleru.
     */
    public function __construct()
    {
        $this->middleware('auth');
        // Middleware IsAdmin pouze pro vytváření, úpravu a mazání
        $this->middleware(IsAdmin::class)->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    /**
     * Zobrazí seznam všech projektových položek.
     */
    public function index(): View
    {
        $projectItems = ProjectItem::with('project.customer')->paginate(10);
        return view('project_items.index', compact('projectItems'));
    }

    /**
     * Zobrazí seznam projektových položek konkrétního projektu.
     */
    public function projectItems(Project $project): View
    {
        $projectItems = ProjectItem::where('id_project', $project->id)->paginate(10);
        return view('project_items.project_items', compact('projectItems', 'project'));
    }

    /**
     * Zobrazí formulář pro vytvoření nové projektové položky.
     */
    public function create(Request $request): View
    {
        $projects = Project::where('state', 1)->with('customer')->get();
        $selectedProject = null;

        // Pokud je předán parametr id_project, předvyplníme projekt
        if ($request->has('id_project')) {
            $selectedProject = Project::with('customer')->findOrFail($request->id_project);
        }

        return view('project_items.create', compact('projects', 'selectedProject'));
    }

    /**
     * Uloží novou projektovou položku do databáze.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id_project' => 'required|exists:projects,id',
            'name' => 'required|string|max:100',
            'state' => 'required|integer',
            'kind' => 'required|integer',
        ]);

        $projectItem = ProjectItem::create($validated);

        // Pokud jsme přišli z detailu projektu, vrátíme se zpět
        if ($request->has('redirect_to_project') && $request->redirect_to_project) {
            return redirect()->route('projects.show', $validated['id_project'])
                ->with('success', 'Položka byla úspěšně vytvořena.');
        }

        return redirect()->route('project_items.index')
            ->with('success', 'Položka byla úspěšně vytvořena.');
    }

    /**
     * Zobrazí detail konkrétní projektové položky.
     */
    public function show(ProjectItem $projectItem): View
    {
        // Načíst přiřazené uživatele
        $projectItem->load([
            'project.customer',
            'users',
            'customerUsers',
            'requests' => function($query) {
                $query->orderBy('created_at', 'desc')->limit(5);
            }
        ]);

        // Získáme všechny uživatele a zákaznické uživatele, které můžeme přiřadit
        $availableUsers = User::where('state', 1)->get();
        $availableCustomerUsers = CustomerUser::where('state', 1)
            ->where('id_customer', $projectItem->project->id_customer)
            ->get();

        return view('project_items.show', compact('projectItem', 'availableUsers', 'availableCustomerUsers'));
    }

    /**
     * Zobrazí formulář pro úpravu projektové položky.
     */
    public function edit(ProjectItem $projectItem): View
    {
        $projects = Project::where('state', 1)->with('customer')->get();
        return view('project_items.edit', compact('projectItem', 'projects'));
    }

    /**
     * Aktualizuje projektovou položku v databázi.
     */
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

    /**
     * Smaže projektovou položku z databáze.
     */
    public function destroy(ProjectItem $projectItem): RedirectResponse
    {
        // Uložíme si ID projektu pro případné přesměrování
        $projectId = $projectItem->id_project;

        // Kontrola, zda má položka přiřazené požadavky
        if ($projectItem->requests()->count() > 0) {
            return back()
                ->with('error', 'Položku nelze smazat, protože má přiřazené požadavky.');
        }

        // Odstraníme všechny vazby na uživatele a zákaznické uživatele
        $projectItem->users()->detach();
        $projectItem->customerUsers()->detach();

        $projectItem->delete();

        // Pokud jsme přišli z URL, která obsahuje cestu k detailu projektu
        if (url()->previous() === route('projects.show', $projectId)) {
            return redirect()->route('projects.show', $projectId)
                ->with('success', 'Položka byla úspěšně smazána.');
        }

        return redirect()->route('project_items.index')
            ->with('success', 'Položka byla úspěšně smazána.');
    }

    /**
     * Přiřadí uživatele k projektové položce.
     */
    public function assignUser(Request $request, ProjectItem $projectItem): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        // Kontrola, zda již není uživatel přiřazen
        if (!$projectItem->users->contains($validated['user_id'])) {
            $projectItem->users()->attach($validated['user_id']);
            return back()->with('success', 'Uživatel byl úspěšně přiřazen.');
        }

        return back()->with('info', 'Uživatel je již přiřazen k této položce.');
    }

    /**
     * Odebere uživatele z projektové položky.
     */
    public function removeUser(ProjectItem $projectItem, User $user): RedirectResponse
    {
        $projectItem->users()->detach($user->id);
        return back()->with('success', 'Uživatel byl odebrán z položky.');
    }

    /**
     * Přiřadí zákaznického uživatele k projektové položce.
     */
    public function assignCustomerUser(Request $request, ProjectItem $projectItem): RedirectResponse
    {
        $validated = $request->validate([
            'customer_user_id' => 'required|exists:customer_users,id'
        ]);

        // Kontrola, zda již není uživatel přiřazen
        if (!$projectItem->customerUsers->contains($validated['customer_user_id'])) {
            $projectItem->customerUsers()->attach($validated['customer_user_id']);
            return back()->with('success', 'Zákaznický uživatel byl úspěšně přiřazen.');
        }

        return back()->with('info', 'Zákaznický uživatel je již přiřazen k této položce.');
    }

    /**
     * Odebere zákaznického uživatele z projektové položky.
     */
    public function removeCustomerUser(ProjectItem $projectItem, CustomerUser $customerUser): RedirectResponse
    {
        $projectItem->customerUsers()->detach($customerUser->id);
        return back()->with('success', 'Zákaznický uživatel byl odebrán z položky.');
    }
}
