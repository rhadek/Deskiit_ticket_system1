<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Username -->
        <div>
            <x-input-label for="username" :value="__('Uživatelské jméno')" />
            <x-text-input id="username" class="block mt-1 w-full" type="text" name="username" :value="old('username')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('username')" class="mt-2" />
        </div>

        <!-- First Name -->
        <div class="mt-4">
            <x-input-label for="fname" :value="__('Jméno')" />
            <x-text-input id="fname" class="block mt-1 w-full" type="text" name="fname" :value="old('fname')" required autocomplete="given-name" />
            <x-input-error :messages="$errors->get('fname')" class="mt-2" />
        </div>

        <!-- Last Name -->
        <div class="mt-4">
            <x-input-label for="lname" :value="__('Příjmení')" />
            <x-text-input id="lname" class="block mt-1 w-full" type="text" name="lname" :value="old('lname')" required autocomplete="family-name" />
            <x-input-error :messages="$errors->get('lname')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="email" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Telephone -->
        <div class="mt-4">
            <x-input-label for="telephone" :value="__('Telefon')" />
            <x-text-input id="telephone" class="block mt-1 w-full" type="tel" name="telephone" :value="old('telephone')" required autocomplete="tel" />
            <x-input-error :messages="$errors->get('telephone')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Heslo')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Potvrzení hesla')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Již máte účet?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Registrovat') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
