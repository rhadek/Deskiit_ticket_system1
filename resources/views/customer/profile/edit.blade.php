<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Zákaznický profil</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Vite Scripts (Laravel 9+) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        <!-- Example of a customer navigation (if any). You can include it here or via @include('partials.customer-nav') -->

        <!-- Page Heading -->
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Profil') }}
                </h2>
            </div>
        </header>

        <!-- Page Content -->
        <main>
            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                        <div class="max-w-xl">
                            <section>
                                <header>
                                    <h2 class="text-lg font-medium text-gray-900">
                                        {{ __('Informace o profilu') }}
                                    </h2>

                                    <p class="mt-1 text-sm text-gray-600">
                                        {{ __("Aktualizujte informace o svém profilu.") }}
                                    </p>
                                </header>

                                <form method="POST" action="{{ route('customer.profile.update') }}" class="mt-6 space-y-6">
                                    @csrf
                                    @method('patch')

                                    <!-- First Name -->
                                    <div>
                                        <x-input-label for="fname" :value="__('Jméno')" />
                                        <x-text-input
                                            id="fname"
                                            name="fname"
                                            type="text"
                                            class="mt-1 block w-full"
                                            :value="old('fname', $user->fname)"
                                            required
                                            autofocus
                                            autocomplete="fname"
                                        />
                                        <x-input-error class="mt-2" :messages="$errors->get('fname')" />
                                    </div>

                                    <!-- Last Name -->
                                    <div>
                                        <x-input-label for="lname" :value="__('Příjmení')" />
                                        <x-text-input
                                            id="lname"
                                            name="lname"
                                            type="text"
                                            class="mt-1 block w-full"
                                            :value="old('lname', $user->lname)"
                                            required
                                            autocomplete="lname"
                                        />
                                        <x-input-error class="mt-2" :messages="$errors->get('lname')" />
                                    </div>

                                    <!-- Email -->
                                    <div>
                                        <x-input-label for="email" :value="__('Email')" />
                                        <x-text-input
                                            id="email"
                                            name="email"
                                            type="email"
                                            class="mt-1 block w-full"
                                            :value="old('email', $user->email)"
                                            required
                                            autocomplete="email"
                                        />
                                        <x-input-error class="mt-2" :messages="$errors->get('email')" />
                                    </div>

                                    <!-- Telephone -->
                                    <div>
                                        <x-input-label for="telephone" :value="__('Telefon')" />
                                        <x-text-input
                                            id="telephone"
                                            name="telephone"
                                            type="text"
                                            class="mt-1 block w-full"
                                            :value="old('telephone', $user->telephone)"
                                            required
                                            autocomplete="telephone"
                                        />
                                        <x-input-error class="mt-2" :messages="$errors->get('telephone')" />
                                    </div>

                                    <div class="flex items-center gap-4">
                                        <x-primary-button>
                                            {{ __('Uložit') }}
                                        </x-primary-button>
                                    </div>
                                </form>
                            </section>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
