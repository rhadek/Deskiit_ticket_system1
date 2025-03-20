<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Vytvoření požadavku</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        @include('customer.layouts.navigation')

        <!-- Page Heading -->
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Vytvořit nový požadavek') }}
                </h2>
            </div>
        </header>

        <!-- Page Content -->
        <main>
            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <form method="POST" action="{{ route('customer.requests.store') }}">
                                @csrf

                                <!-- Projektová položka -->
                                <div>
                                    <x-input-label for="id_projectitem" :value="__('Projektová položka')" />
                                    <select id="id_projectitem" name="id_projectitem" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">
                                        @foreach($projectItems as $item)
                                            <option value="{{ $item->id }}">
                                                {{ $item->name }} ({{ $item->project->name }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('id_projectitem')" class="mt-2" />
                                </div>

                                <!-- Název požadavku -->
                                <div class="mt-4">
                                    <x-input-label for="name" :value="__('Název požadavku')" />
                                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </div>

                                <!-- Popis požadavku -->
                                <div class="mt-4">
                                    <x-input-label for="description" :value="__('Popis požadavku')" />
                                    <textarea id="description" name="description" rows="5" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" required>{{ old('description') }}</textarea>
                                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                                </div>

                                <!-- Typ -->
                                <div class="mt-4">
                                    <x-input-label for="kind" :value="__('Typ')" />
                                    <select id="kind" name="kind" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">
                                        <option value="1" selected>Standardní</option>
                                        <option value="2">Chyba</option>
                                        <option value="3">Prioritní</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('kind')" class="mt-2" />
                                </div>

                                <div class="flex items-center justify-end mt-4">
                                    <a href="{{ route('customer.requests.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-3">
                                        {{ __('Zrušit') }}
                                    </a>
                                    <x-primary-button class="ml-4">
                                        {{ __('Vytvořit požadavek') }}
                                    </x-primary-button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
