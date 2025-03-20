<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Vytvořit nového uživatele firmy') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('customer_users.store') }}">
                        @csrf

                        <!-- Firma -->
                        <div>
                            <x-input-label for="id_customer" :value="__('Firma')" />
                            <select id="id_customer" name="id_customer" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ $selectedCustomer && $selectedCustomer->id == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('id_customer')" class="mt-2" />
                        </div>

                        <!-- Uživatelské jméno -->
                        <div class="mt-4">
                            <x-input-label for="username" :value="__('Uživatelské jméno')" />
                            <x-text-input id="username" class="block mt-1 w-full" type="text" name="username" :value="old('username')" required autofocus />
                            <x-input-error :messages="$errors->get('username')" class="mt-2" />
                        </div>

                        <!-- Jméno -->
                        <div class="mt-4">
                            <x-input-label for="fname" :value="__('Jméno')" />
                            <x-text-input id="fname" class="block mt-1 w-full" type="text" name="fname" :value="old('fname')" required />
                            <x-input-error :messages="$errors->get('fname')" class="mt-2" />
                        </div>

                        <!-- Příjmení -->
                        <div class="mt-4">
                            <x-input-label for="lname" :value="__('Příjmení')" />
                            <x-text-input id="lname" class="block mt-1 w-full" type="text" name="lname" :value="old('lname')" required />
                            <x-input-error :messages="$errors->get('lname')" class="mt-2" />
                        </div>

                        <!-- Email -->
                        <div class="mt-4">
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- Telefon -->
                        <div class="mt-4">
                            <x-input-label for="telephone" :value="__('Telefon')" />
                            <x-text-input id="telephone" class="block mt-1 w-full" type="text" name="telephone" :value="old('telephone')" required />
                            <x-input-error :messages="$errors->get('telephone')" class="mt-2" />
                        </div>

                        <!-- Heslo -->
                        <div class="mt-4">
                            <x-input-label for="password" :value="__('Heslo')" />
                            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <!-- Potvrzení hesla -->
                        <div class="mt-4">
                            <x-input-label for="password_confirmation" :value="__('Potvrzení hesla')" />
                            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                        </div>

                        <!-- Stav -->
                        <div class="mt-4">
                            <x-input-label for="state" :value="__('Stav')" />
                            <select id="state" name="state" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">
                                <option value="1" selected>Aktivní</option>
                                <option value="0">Neaktivní</option>
                            </select>
                            <x-input-error :messages="$errors->get('state')" class="mt-2" />
                        </div>

                        <!-- Typ (kind) -->
                        <div class="mt-4">
                            <x-input-label for="kind" :value="__('Role')" />
                            <select id="kind" name="kind" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">
                                <option value="1" selected>Běžný uživatel</option>
                                <option value="2">Pokročilý uživatel</option>
                                <option value="3">Admin</option>
                            </select>
                            <x-input-error :messages="$errors->get('kind')" class="mt-2" />
                        </div>

                        <!-- Přesměrování zpět k firmě -->
                        @if($selectedCustomer)
                            <input type="hidden" name="redirect_to_customer" value="1">
                        @endif

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ $selectedCustomer ? route('customers.show', $selectedCustomer) : route('customer_users.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-3">
                                {{ __('Zrušit') }}
                            </a>
                            <x-primary-button class="ml-4">
                                {{ __('Vytvořit uživatele') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
