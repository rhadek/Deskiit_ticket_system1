<x-customer-layout>
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
                    <h2 class="text-2xl font-bold mb-2">Dobrý den, {{ Auth::guard('customer')->user()->fname }}!</h2>
                    <p class="text-gray-600">
                        Vítejte v portálu firmy
                        <strong>{{ Auth::guard('customer')->user()->customer->name }}</strong>
                    </p>
                </div>
            </div>

            <!-- Přehled projektů -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-semibold">Naše projekty</h3>
                        <a href="{{ route('customer.projects.index') }}" class="text-blue-600 hover:text-blue-800">
                            Zobrazit vše
                        </a>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @forelse($project_items as $item)
                            <div class="border rounded-lg p-4 hover:shadow-md transition">
                                <div class="flex justify-between items-center mb-2">
                                    <a href="{{ route('customer.project_items.show', $item) }}" class="text-lg font-medium text-blue-600 hover:text-blue-800">
                                        {{ $item->name }}
                                    </a>
                                    @if ($item->state == 1)
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Aktivní</span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Neaktivní</span>
                                    @endif
                                </div>
                                <div class="text-sm text-gray-600">
                                    Projekt: {{ $item->project->name }}
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 col-span-full text-center">
                                Nemáte přiřazené žádné projektové položky
                            </p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Statistiky požadavků -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Požadavky -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-xl font-semibold">Požadavky</h3>
                            <a href="{{ route('customer.requests.index') }}" class="text-blue-600 hover:text-blue-800">
                                Zobrazit vše
                            </a>
                        </div>

                        <!-- Graf stavů požadavků -->
                        <div class="mb-4">
                            <div class="flex justify-between mb-2">
                                <span class="text-sm text-gray-600">Stav požadavků</span>
                                <span class="text-sm font-semibold">{{ $metrics['total_requests'] }} celkem</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-4">
                                <div class="bg-blue-600 h-4 rounded-full" style="width: {{ $metrics['total_requests'] > 0 ? ($metrics['open_requests'] / $metrics['total_requests'] * 100) : 0 }}%"></div>
                            </div>
                            <div class="flex justify-between mt-1 text-xs text-gray-600">
                                <span>Otevřené: {{ $metrics['open_requests'] }}</span>
                                <span>Vyřešené: {{ $metrics['resolved_requests'] }}</span>
                            </div>
                        </div>

                        <!-- Poslední požadavky -->
                        <div>
                            <h4 class="text-lg font-medium mb-3">Poslední požadavky</h4>
                            <div class="space-y-3">
                                @forelse($recent_requests as $request)
                                    <div class="border-b pb-2 last:border-b-0">
                                        <a href="{{ route('customer.requests.show', $request) }}" class="text-blue-600 hover:text-blue-800">
                                            {{ $request->name }}
                                        </a>
                                        <div class="text-xs text-gray-500 flex justify-between">
                                            <span>{{ $request->projectItem->project->name }}</span>
                                            <span>
                                                @switch($request->state)
                                                    @case(1)
                                                        <span class="text-blue-600">Nový</span>
                                                        @break
                                                    @case(2)
                                                        <span class="text-yellow-600">V řešení</span>
                                                        @break
                                                    @case(4)
                                                        <span class="text-green-600">Vyřešeno</span>
                                                        @break
                                                    @case(5)
                                                        <span class="text-gray-600">Uzavřeno</span>
                                                        @break
                                                @endswitch
                                            </span>
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
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-xl font-semibold mb-4">Rychlé akce</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <a href="{{ route('customer.requests.create') }}" class="bg-blue-50 hover:bg-blue-100 rounded-lg p-4 text-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto mb-2 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                <span class="text-sm text-blue-800">Nový požadavek</span>
                            </a>
                            <a href="{{ route('customer.profile') }}" class="bg-green-50 hover:bg-green-100 rounded-lg p-4 text-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto mb-2 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <span class="text-sm text-green-800">Můj profil</span>
                            </a>
                            <a href="{{ route('customer.projects.index') }}" class="bg-purple-50 hover:bg-purple-100 rounded-lg p-4 text-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto mb-2 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                </svg>
                                <span class="text-sm text-purple-800">Projekty</span>
                            </a>
                            <a href="{{ route('customer.requests.index') }}" class="bg-yellow-50 hover:bg-yellow-100 rounded-lg p-4 text-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto mb-2 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span class="text-sm text-yellow-800">Moje požadavky</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-customer-layout>
