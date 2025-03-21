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

        // Pokud je uživatel admin (kind=3), zobrazíme všechny projekty firmy
        if ($user->kind == 3) {
            $projects = Project::where('id_customer', $user->id_customer)
                ->with('customer')
                ->paginate(10);
        } else {
            // Původní kód pro běžné uživatele - pouze projekty, ke kterým mají přístup
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

        // Kontrola, zda projekt patří ke stejnému zákazníkovi jako uživatel
        if ($project->id_customer !== $user->id_customer) {
            abort(403, 'Neautorizovaný přístup.');
        }

        // Pro adminy (kind=3) zobrazíme všechny položky projektu
        if ($user->kind == 3) {
            $projectItems = ProjectItem::where('id_project', $project->id)
                ->paginate(10);
        } else {
            // Pro běžné uživatele jen přiřazené položky
            $projectItems = $user->projectItems()
                ->where('id_project', $project->id)
                ->paginate(10);
        }

        $project->load('customer');
        return view('customer.projects.show', compact('project', 'projectItems'));
    }

    public function create()
    {
        // Zkontrolovat, zda je uživatel admin
        $user = Auth::guard('customer')->user();
        if ($user->kind != 3) {
            abort(403, 'Nemáte oprávnění vytvářet projekty.');
        }

        return view('customer.projects.create', [
            'customer' => $user->customer,
        ]);
    }

    // Nová metoda pro uložení projektu
    public function store(Request $request)
    {
        // Zkontrolovat, zda je uživatel admin
        $user = Auth::guard('customer')->user();
        if ($user->kind != 3) {
            abort(403, 'Nemáte oprávnění vytvářet projekty.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'state' => 'required|integer|in:0,1',
            'kind' => 'required|integer|in:1,2,3',
        ]);

        // Přidat ID zákazníka z přihlášeného uživatele
        $validated['id_customer'] = $user->id_customer;

        $project = Project::create($validated);

        return redirect()->route('customer.projects.show', $project)
            ->with('success', 'Projekt byl úspěšně vytvořen.');
    }

    // Nová metoda pro editaci projektu
    public function edit(Project $project)
    {
        // Zkontrolovat, zda je uživatel admin a projekt patří jeho firmě
        $user = Auth::guard('customer')->user();
        if ($user->kind != 3 || $project->id_customer != $user->id_customer) {
            abort(403, 'Nemáte oprávnění upravovat tento projekt.');
        }

        return view('customer.projects.edit', compact('project'));
    }

    // Nová metoda pro aktualizaci projektu
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

    // Nová metoda pro smazání projektu
    public function destroy(Project $project)
    {
        // Zkontrolovat, zda je uživatel admin a projekt patří jeho firmě
        $user = Auth::guard('customer')->user();
        if ($user->kind != 3 || $project->id_customer != $user->id_customer) {
            abort(403, 'Nemáte oprávnění smazat tento projekt.');
        }

        // Kontrola, zda má projekt přiřazené položky
        if ($project->projectItems()->count() > 0) {
            return back()->with('error', 'Projekt nelze smazat, protože obsahuje položky.');
        }

        $project->delete();

        return redirect()->route('customer.projects.index')
            ->with('success', 'Projekt byl úspěšně smazán.');
    }
}
