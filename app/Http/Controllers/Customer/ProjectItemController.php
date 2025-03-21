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

        // Načteme projekt, abychom mohli zkontrolovat, zda patří ke stejné firmě
        $projectItem->load('project');

        // Kontrola, zda projektová položka patří ke stejnému zákazníkovi jako uživatel
        if ($projectItem->project->id_customer !== $user->id_customer) {
            abort(403, 'Neautorizovaný přístup.');
        }

        // Pro běžné uživatele kontrolujeme přímý přístup
        if ($user->kind != 3 && !$user->projectItems()->where('project_items.id', $projectItem->id)->exists()) {
            abort(403, 'Nemáte oprávnění zobrazit tuto projektovou položku.');
        }

        // Načtení relationálních dat - pro adminy všechny požadavky pro tuto položku
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
            // Původní kód - jen požadavky daného uživatele
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
        // Zkontrolovat, zda je uživatel admin
        $user = Auth::guard('customer')->user();
        if ($user->kind != 3) {
            abort(403, 'Nemáte oprávnění vytvářet projektové položky.');
        }

        $selectedProject = null;

        // Pokud je předáno ID projektu, načteme ho a zkontrolujeme přístup
        if ($request->has('id_project')) {
            $selectedProject = Project::findOrFail($request->id_project);
            if ($selectedProject->id_customer != $user->id_customer) {
                abort(403, 'Nemáte oprávnění k tomuto projektu.');
            }
        }

        // Načteme všechny projekty firmy
        $projects = Project::where('id_customer', $user->id_customer)
            ->where('state', 1)
            ->get();

        return view('customer.project_items.create', compact('projects', 'selectedProject'));
    }

    // Nová metoda pro uložení položky projektu
    public function store(Request $request)
    {
        // Zkontrolovat, zda je uživatel admin
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

        // Zkontrolovat, zda projekt patří firmě uživatele
        $project = Project::findOrFail($validated['id_project']);
        if ($project->id_customer != $user->id_customer) {
            abort(403, 'Nemáte oprávnění k tomuto projektu.');
        }

        $projectItem = ProjectItem::create($validated);

        // Automaticky přiřadit vytvořenou položku admin uživateli
        $projectItem->customerUsers()->attach($user->id);

        if ($request->has('redirect_to_project') && $request->redirect_to_project) {
            return redirect()->route('customer.projects.show', $project)
                ->with('success', 'Položka byla úspěšně vytvořena.');
        }

        return redirect()->route('customer.project_items.show', $projectItem)
            ->with('success', 'Položka byla úspěšně vytvořena.');
    }

    // Nová metoda pro editaci položky projektu
    public function edit(ProjectItem $projectItem)
    {
        // Zkontrolovat, zda je uživatel admin
        $user = Auth::guard('customer')->user();
        if ($user->kind != 3) {
            abort(403, 'Nemáte oprávnění upravovat projektové položky.');
        }

        // Načíst projektovou položku s projektem pro kontrolu přístupu
        $projectItem->load('project');

        // Kontrola, zda položka patří firmě uživatele
        if ($projectItem->project->id_customer != $user->id_customer) {
            abort(403, 'Nemáte oprávnění k této projektové položce.');
        }

        // Načteme všechny projekty firmy
        $projects = Project::where('id_customer', $user->id_customer)
            ->where('state', 1)
            ->get();

        return view('customer.project_items.edit', compact('projectItem', 'projects'));
    }

    // Nová metoda pro aktualizaci položky projektu
    public function update(Request $request, ProjectItem $projectItem)
    {
        // Zkontrolovat, zda je uživatel admin
        $user = Auth::guard('customer')->user();
        if ($user->kind != 3) {
            abort(403, 'Nemáte oprávnění upravovat projektové položky.');
        }

        // Načíst projektovou položku s projektem pro kontrolu přístupu
        $projectItem->load('project');

        // Kontrola, zda položka patří firmě uživatele
        if ($projectItem->project->id_customer != $user->id_customer) {
            abort(403, 'Nemáte oprávnění k této projektové položce.');
        }

        $validated = $request->validate([
            'id_project' => 'required|exists:projects,id',
            'name' => 'required|string|max:100',
            'state' => 'required|integer|in:0,1',
            'kind' => 'required|integer|in:1,2,3',
        ]);

        // Zkontrolovat, zda nový projekt patří firmě uživatele
        $project = Project::findOrFail($validated['id_project']);
        if ($project->id_customer != $user->id_customer) {
            abort(403, 'Nemáte oprávnění k tomuto projektu.');
        }

        $projectItem->update($validated);

        return redirect()->route('customer.project_items.show', $projectItem)
            ->with('success', 'Položka byla úspěšně aktualizována.');
    }

    // Nová metoda pro smazání položky projektu
    public function destroy(ProjectItem $projectItem)
    {
        // Zkontrolovat, zda je uživatel admin
        $user = Auth::guard('customer')->user();
        if ($user->kind != 3) {
            abort(403, 'Nemáte oprávnění mazat projektové položky.');
        }

        // Načíst projektovou položku s projektem pro kontrolu přístupu
        $projectItem->load('project');

        // Kontrola, zda položka patří firmě uživatele
        if ($projectItem->project->id_customer != $user->id_customer) {
            abort(403, 'Nemáte oprávnění k této projektové položce.');
        }

        // Kontrola, zda má položka přiřazené požadavky
        if ($projectItem->requests()->count() > 0) {
            return back()->with('error', 'Položku nelze smazat, protože obsahuje požadavky.');
        }

        // Uložíme si ID projektu pro přesměrování
        $projectId = $projectItem->id_project;

        // Odstraníme všechny vazby na uživatele
        $projectItem->customerUsers()->detach();

        $projectItem->delete();

        return redirect()->route('customer.projects.show', $projectId)
            ->with('success', 'Položka byla úspěšně smazána.');
    }

    // Metoda pro správu přiřazení uživatelů k projektové položce
    public function assignUsers(ProjectItem $projectItem)
    {
        // Zkontrolovat, zda je uživatel admin
        $user = Auth::guard('customer')->user();
        if ($user->kind != 3) {
            abort(403, 'Nemáte oprávnění spravovat přístupy k projektovým položkám.');
        }

        // Načíst projektovou položku s projektem pro kontrolu přístupu
        $projectItem->load(['project', 'customerUsers']);

        // Kontrola, zda položka patří firmě uživatele
        if ($projectItem->project->id_customer != $user->id_customer) {
            abort(403, 'Nemáte oprávnění k této projektové položce.');
        }

        // Načteme všechny uživatele firmy
        $customerUsers = CustomerUser::where('id_customer', $user->id_customer)
            ->where('state', 1)
            ->get();

        return view('customer.project_items.assign_users', compact('projectItem', 'customerUsers'));
    }

    // Metoda pro uložení přiřazení uživatelů
    public function storeAssignments(Request $request, ProjectItem $projectItem)
    {
        // Zkontrolovat, zda je uživatel admin
        $user = Auth::guard('customer')->user();
        if ($user->kind != 3) {
            abort(403, 'Nemáte oprávnění spravovat přístupy k projektovým položkám.');
        }

        // Načíst projektovou položku s projektem pro kontrolu přístupu
        $projectItem->load('project');

        // Kontrola, zda položka patří firmě uživatele
        if ($projectItem->project->id_customer != $user->id_customer) {
            abort(403, 'Nemáte oprávnění k této projektové položce.');
        }

        $validated = $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:customer_users,id',
        ]);

        // Zkontrolovat, zda všichni uživatelé patří do stejné firmy
        $count = CustomerUser::whereIn('id', $validated['user_ids'])
            ->where('id_customer', $user->id_customer)
            ->count();

        if ($count != count($validated['user_ids'])) {
            abort(403, 'Některý z vybraných uživatelů nepatří do vaší firmy.');
        }

        // Aktualizovat přiřazení
        $projectItem->customerUsers()->sync($validated['user_ids']);

        return redirect()->route('customer.project_items.show', $projectItem)
            ->with('success', 'Přístupy byly úspěšně aktualizovány.');
    }
}
