<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('customer.profile.edit', [
            'user' => Auth::guard('customer')->user(),
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::guard('customer')->user();

        $validated = $request->validate([
            'fname' => ['required', 'string', 'max:100'],
            'lname' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:100', Rule::unique('customer_users')->ignore($user->id)],
            'telephone' => ['required', 'string', 'max:10'],
        ]);

        if ($request->filled('current_password') && $request->filled('password')) {
            $request->validate([
                'current_password' => ['required', function ($attribute, $value, $fail) use ($user) {
                    if (!Hash::check($value, $user->password)) {
                        $fail('Zadané aktuální heslo není správné.');
                    }
                }],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
            ]);

            $validated['password'] = Hash::make($request->password);
        }

        $user->update($validated);

        return back()->with('success', 'Profil byl úspěšně aktualizován.');
    }
}
