<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\View\View;
use App\Models\CustomerUser;
use Illuminate\Http\Request;
use App\Http\Middleware\IsAdmin;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;

class CustomerUserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(IsAdmin::class)->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index(): View
    {
        $customerUsers = CustomerUser::with('customer')->paginate(10);
        return view('customer_users.index', compact('customerUsers'));
    }

    public function customerUsers(Customer $customer): View
    {
        $customerUsers = CustomerUser::where('id_customer', $customer->id)->paginate(10);
        return view('customer_users.customer_users', compact('customerUsers', 'customer'));
    }

    public function create(Request $request): View
    {
        $customers = Customer::where('state', 1)->get();
        $selectedCustomer = null;

        if ($request->has('id_customer')) {
            $selectedCustomer = Customer::findOrFail($request->id_customer);
        }

        return view('customer_users.create', compact('customers', 'selectedCustomer'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id_customer' => 'required|exists:customers,id',
            'username' => 'required|string|max:100|unique:customer_users',
            'password' => 'required|string|min:8|confirmed',
            'fname' => 'required|string|max:100',
            'lname' => 'required|string|max:100',
            'email' => 'required|string|email|max:100|unique:customer_users',
            'telephone' => 'required|string|max:10',
            'state' => 'required|integer',
            'kind' => 'required|integer',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        CustomerUser::create($validated);

        if ($request->has('redirect_to_customer') && $request->redirect_to_customer) {
            return redirect()->route('customers.show', $validated['id_customer'])
                ->with('success', 'Uživatel byl úspěšně vytvořen.');
        }

        return redirect()->route('customer_users.index')
            ->with('success', 'Uživatel byl úspěšně vytvořen.');
    }

    public function show(CustomerUser $customerUser): View
    {
        $customerUser->load(['customer', 'projectItems', 'requests']);

        return view('customer_users.show', compact('customerUser'));
    }

    public function edit(CustomerUser $customerUser): View
    {
        $customers = Customer::where('state', 1)->get();
        return view('customer_users.edit', compact('customerUser', 'customers'));
    }

    public function update(Request $request, CustomerUser $customerUser): RedirectResponse
    {
        $validated = $request->validate([
            'id_customer' => 'required|exists:customers,id',
            'username' => 'required|string|max:100|unique:customer_users,username,' . $customerUser->id,
            'fname' => 'required|string|max:100',
            'lname' => 'required|string|max:100',
            'email' => 'required|string|email|max:100|unique:customer_users,email,' . $customerUser->id,
            'telephone' => 'required|string|max:10',
            'state' => 'required|integer',
            'kind' => 'required|integer',
        ]);

        if ($request->filled('password')) {
            $request->validate([
                'password' => 'string|min:8|confirmed',
            ]);
            $validated['password'] = Hash::make($request->password);
        }

        $customerUser->update($validated);

        return redirect()->route('customer_users.show', $customerUser)
            ->with('success', 'Informace o uživateli byly úspěšně aktualizovány.');
    }

    public function destroy(CustomerUser $customerUser): RedirectResponse
    {
        $customerId = $customerUser->id_customer;

        if ($customerUser->projectItems()->count() > 0 || $customerUser->requests()->count() > 0) {
            return back()
                ->with('error', 'Uživatele nelze smazat, protože má přiřazené projektové položky nebo požadavky.');
        }

        $customerUser->delete();

        if (url()->previous() === route('customers.show', $customerId)) {
            return redirect()->route('customers.show', $customerId)
                ->with('success', 'Uživatel byl úspěšně smazán.');
        }

        return redirect()->route('customer_users.index')
            ->with('success', 'Uživatel byl úspěšně smazán.');
    }
}
