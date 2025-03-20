<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Http\RedirectResponse;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(): View
    {
        $customers = Customer::paginate(10);
        return view('customers.index', compact('customers'));
    }

    public function create(): View
    {
        return view('customers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'ic' => 'required|string|max:100|unique:customers',
            'state' => 'required|integer',
            'kind' => 'required|integer',
        ]);

        Customer::create($validated);

        return redirect()->route('customers.index')
            ->with('success', 'Firma byla úspěšně vytvořena.');
    }

    public function show(Customer $customer): View
    {
        // Načíst související data pro firmu
        $customer->load(['customerUsers', 'projects']);

        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer): View
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'ic' => 'required|string|max:100|unique:customers,ic,' . $customer->id,
            'state' => 'required|integer',
            'kind' => 'required|integer',
        ]);

        $customer->update($validated);

        return redirect()->route('customers.show', $customer)
            ->with('success', 'Informace o firmě byly úspěšně aktualizovány.');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        // Kontrola, zda má firma přiřazené uživatele nebo projekty
        if ($customer->customerUsers()->count() > 0 || $customer->projects()->count() > 0) {
            return redirect()->route('customers.index')
                ->with('error', 'Firmu nelze smazat, protože má přiřazené uživatele nebo projekty.');
        }

        $customer->delete();

        return redirect()->route('customers.index')
            ->with('success', 'Firma byla úspěšně smazána.');
    }
}
