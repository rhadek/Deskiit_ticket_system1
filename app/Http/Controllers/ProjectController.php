<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Http\Middleware\IsAdmin;
use Illuminate\Routing\Controller;

class ProjectController extends Controller
{
    /**
     * Vytvoření instance nového controlleru.
     */
    public function __construct()
    {
        $this->middleware('auth');
        // Middleware IsAdmin pro všechny metody - pouze admin může pracovat s projekty
        $this->middleware(IsAdmin::class);
    }

    /**
     * Zobrazí seznam všech projektů.
     */
    public function index(): View
    {
        $projects = Project::with('customer')->paginate(10);
        return view('projects.index', compact('projects'));
    }

    /**
     * Zobrazí formulář pro vytvoření nového projektu.
     */
    public function create(Request $request): View
    {
        $customers = Customer::where('state', 1)->get();
        $selectedCustomer = null;

        // Pokud je předán parametr id_customer, předvyplníme zákazníka
        if ($request->has('id_customer')) {
            $selectedCustomer = Customer::findOrFail($request->id_customer);
        }

        return view('projects.create', compact('customers', 'selectedCustomer'));
    }

    /**
     * Uloží nový projekt do databáze.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id_customer' => 'required|exists:customers,id',
            'name' => 'required|string|max:100',
            'state' => 'required|integer',
            'kind' => 'required|integer',
        ]);

        $project = Project::create($validated);

        // Pokud jsme přišli z detailu zákazníka, vrátíme se zpět
        if ($request->has('redirect_to_customer') && $request->redirect_to_customer) {
            return redirect()->route('customers.show', $validated['id_customer'])
                ->with('success', 'Projekt byl úspěšně vytvořen.');
        }

        return redirect()->route('projects.index')
            ->with('success', 'Projekt byl úspěšně vytvořen.');
    }

    /**
     * Zobrazí detail konkrétního projektu.
     */
    public function show(Project $project): View
    {
        // Načíst související data pro projekt
        $project->load(['customer', 'projectItems']);

        return view('projects.show', compact('project'));
    }

    /**
     * Zobrazí formulář pro úpravu projektu.
     */
    public function edit(Project $project): View
    {
        $customers = Customer::where('state', 1)->get();
        return view('projects.edit', compact('project', 'customers'));
    }

    /**
     * Aktualizuje projekt v databázi.
     */
    public function update(Request $request, Project $project): RedirectResponse
    {
        $validated = $request->validate([
            'id_customer' => 'required|exists:customers,id',
            'name' => 'required|string|max:100',
            'state' => 'required|integer',
            'kind' => 'required|integer',
        ]);

        $project->update($validated);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Projekt byl úspěšně aktualizován.');
    }

    /**
     * Smaže projekt z databáze.
     */
    public function destroy(Project $project): RedirectResponse
    {
        // Uložíme si ID zákazníka pro případné přesměrování
        $customerId = $project->id_customer;

        // Kontrola, zda má projekt přiřazené položky
        if ($project->projectItems()->count() > 0) {
            return back()
                ->with('error', 'Projekt nelze smazat, protože obsahuje položky.');
        }

        $project->delete();

        // Pokud jsme přišli z URL, která obsahuje cestu k detailu zákazníka
        if (url()->previous() === route('customers.show', $customerId)) {
            return redirect()->route('customers.show', $customerId)
                ->with('success', 'Projekt byl úspěšně smazán.');
        }

        return redirect()->route('projects.index')
            ->with('success', 'Projekt byl úspěšně smazán.');
    }

    /**
     * Zobrazí seznam projektů konkrétního zákazníka.
     */
    public function customerProjects(Customer $customer): View
    {
        $projects = Project::where('id_customer', $customer->id)->paginate(10);
        return view('projects.customer_projects', compact('projects', 'customer'));
    }
}
