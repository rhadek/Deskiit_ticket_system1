<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Můj dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Uvítání -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h2 class="text-2xl font-bold mb-2">Dobrý den, {{ Auth::user()->fname }}!</h2>
                    <p class="text-gray-600">Vítejte v přehledu vašich projektů a požadavků.</p>
                </div>
            </div>

            <!-- Statistiky požadavků -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">Celkové požadavky</h3>
                                <p class="text-3xl font-bold text-indigo-600">{{ $metrics['total_requests'] }}</p>
                            </div>
                            <div class="bg-indigo-100 p-3 rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">Otevřené požadavky</h3>
                                <p class="text-3xl font-bold text-yellow-600">{{ $metrics['open_requests'] }}</p>
                            </div>
                            <div class="bg-yellow-100 p-3 rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">Vyřešené požadavky</h3>
                                <p class="text-3xl font-bold text-green-600">{{ $metrics['resolved_requests'] }}</p>
                            </div>
                            <div class="bg-green-100 p-3 rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Nejnovější projekty a požadavky -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Projekty -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-800">Moje projekty</h3>
                            <a href="{{ route('project_items.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">
                                Zobrazit vše
                            </a>
                        </div>
                        <div class="space-y-4">
                            @forelse ($project_items as $item)
                                <div class="border rounded-lg p-4 hover:shadow-md transition">
                                    <div class="flex justify-between items-center">
                                        <a href="{{ route('project_items.show', $item) }}" class="text-blue-600 hover:text-blue-900 font-medium">
                                            {{ $item->name }}
                                        </a>
                                        @if ($item->state == 1)
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Aktivní</span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Neaktivní</span>
                                        @endif
                                    </div>
                                    <div class="text-sm text-gray-600 mt-1">
                                        Projekt: {{ $item->project->name }}
                                        ({{ $item->project->customer->name }})
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-500 text-center">Nemáte přiřazené žádné projektové položky</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Požadavky -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-800">Nejnovější požadavky</h3>
                            <a href="{{ route('requests.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">
                                Zobrazit vše
                            </a>
                        </div>
                        <div class="space-y-4">
                            @forelse ($recent_requests as $request)
                                <div class="border-b pb-3 last:border-b-0">
                                    <div class="flex justify-between items-center">
                                        <a href="{{ route('requests.show', $request) }}" class="text-blue-600 hover:text-blue-900 font-medium">
                                            {{ $request->name }}
                                        </a>
                                        @switch($request->state)
                                            @case(1)
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Nový</span>
                                                @break
                                            @case(2)
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">V řešení</span>
                                                @break
                                            @case(4)
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Vyřešeno</span>
                                                @break
                                            @case(5)
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Uzavřeno</span>
                                                @break
                                        @endswitch
                                    </div>
                                    <div class="text-sm text-gray-600 mt-1">
                                        Projekt: {{ $request->projectItem->project->name }}
                                        ({{ $request->projectItem->project->customer->name }})
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        Zadavatel: {{ $request->customerUser->fname }} {{ $request->customerUser->lname }}
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-500 text-center">Žádné požadavky</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rychlé akce -->
            <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="{{ route('project_items.index') }}" class="bg-blue-50 hover:bg-blue-100 rounded-lg p-4 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto mb-2 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                    </svg>
                    <span class="text-sm text-blue-800">Projektové položky</span>
                </a>
                <a href="{{ route('requests.create') }}" class="bg-green-50 hover:bg-green-100 rounded-lg p-4 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto mb-2 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span class="text-sm text-green-800">Nový požadavek</span>
                </a>
                <a href="{{ route('profile.edit') }}" class="bg-purple-50 hover:bg-purple-100 rounded-lg p-4 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto mb-2 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span class="text-sm text-purple-800">Profil</span>
                </a>
                <a href="{{ route('requests.index') }}" class="bg-yellow-50 hover:bg-yellow-100 rounded-lg p-4 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto mb-2 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span class="text-sm text-yellow-800">Moje požadavky</span>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
