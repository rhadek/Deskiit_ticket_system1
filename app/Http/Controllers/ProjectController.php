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

    public function __construct()
    {
        $this->middleware('auth');
        // Middleware IsAdmin pro všechny metody - pouze admin může pracovat s projekty
        $this->middleware(IsAdmin::class);
    }

    public function index(): View
    {
        $projects = Project::with('customer')->paginate(10);
        return view('projects.index', compact('projects'));
    }

    public function create(Request $request): View
    {
        $customers = Customer::where('state', 1)->get();
        $selectedCustomer = null;

        if ($request->has('id_customer')) {
            $selectedCustomer = Customer::findOrFail($request->id_customer);
        }

        return view('projects.create', compact('customers', 'selectedCustomer'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id_customer' => 'required|exists:customers,id',
            'name' => 'required|string|max:100',
            'state' => 'required|integer',
            'kind' => 'required|integer',
        ]);

        $project = Project::create($validated);

        if ($request->has('redirect_to_customer') && $request->redirect_to_customer) {
            return redirect()->route('customers.show', $validated['id_customer'])
                ->with('success', 'Projekt byl úspěšně vytvořen.');
        }

        return redirect()->route('projects.index')
            ->with('success', 'Projekt byl úspěšně vytvořen.');
    }

    public function show(Project $project): View
    {
        // Načíst související data pro projekt
        $project->load(['customer', 'projectItems']);

        return view('projects.show', compact('project'));
    }

    public function edit(Project $project): View
    {
        $customers = Customer::where('state', 1)->get();
        return view('projects.edit', compact('project', 'customers'));
    }

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

    public function destroy(Project $project): RedirectResponse
    {
        $customerId = $project->id_customer;

        if ($project->projectItems()->count() > 0) {
            return back()
                ->with('error', 'Projekt nelze smazat, protože obsahuje položky.');
        }

        $project->delete();

        if (url()->previous() === route('customers.show', $customerId)) {
            return redirect()->route('customers.show', $customerId)
                ->with('success', 'Projekt byl úspěšně smazán.');
        }

        return redirect()->route('projects.index')
            ->with('success', 'Projekt byl úspěšně smazán.');
    }

    public function customerProjects(Customer $customer): View
    {
        $projects = Project::where('id_customer', $customer->id)->paginate(10);
        return view('projects.customer_projects', compact('projects', 'customer'));
    }
}
