<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Vytvořit nový report práce') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <!-- Timer Panel -->
                    <div class="bg-gray-50 p-6 rounded-lg mb-6 border">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Stopky</h3>
                        <div class="flex items-center justify-center mb-6">
                            <div id="timer-display" class="text-4xl font-bold text-indigo-700 tracking-wider">
                                00:00:00
                            </div>
                        </div>
                        <div class="flex justify-center space-x-4">
                            <button id="start-timer" type="button" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:border-green-700 focus:ring ring-green-300 disabled:opacity-50 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ __('Spustit') }}
                            </button>
                            <button id="stop-timer" type="button" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:border-red-700 focus:ring ring-red-300 disabled:opacity-50 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z" />
                                </svg>
                                {{ __('Zastavit') }}
                            </button>
                        </div>
                        <p class="text-sm text-gray-500 mt-4 text-center">
                            Pro přesné měření času použijte stopky. Čas se automaticky přenese do formuláře po zastavení.
                        </p>
                    </div>

                    <form method="POST" action="{{ route('request-reports.store') }}">
                        @csrf

                        <!-- Požadavek - pouze pro zobrazení -->
                        <div>
                            <x-input-label for="request" :value="__('Požadavek')" />
                            <x-text-input id="request" class="block mt-1 w-full bg-gray-100" type="text" value="{{ $ticketRequest->name }} ({{ $ticketRequest->projectItem->name }} - {{ $ticketRequest->projectItem->project->name }})" disabled />
                            <input type="hidden" name="id_request" value="{{ $ticketRequest->id }}">
                        </div>

                        <!-- Začátek práce -->
                        <div class="mt-4">
                            <x-input-label for="work_start" :value="__('Začátek práce')" />
                            <x-text-input id="work_start" class="block mt-1 w-full" type="datetime-local" name="work_start" :value="old('work_start')" required />
                            <x-input-error :messages="$errors->get('work_start')" class="mt-2" />
                        </div>

                        <!-- Konec práce -->
                        <div class="mt-4">
                            <x-input-label for="work_end" :value="__('Konec práce')" />
                            <x-text-input id="work_end" class="block mt-1 w-full" type="datetime-local" name="work_end" :value="old('work_end')" required />
                            <x-input-error :messages="$errors->get('work_end')" class="mt-2" />
                        </div>

                        <!-- Celkový čas v minutách -->
                        <div class="mt-4">
                            <x-input-label for="work_total" :value="__('Celkový čas (minuty)')" />
                            <x-text-input id="work_total" class="block mt-1 w-full" type="number" name="work_total" :value="old('work_total')" required min="1" />
                            <x-input-error :messages="$errors->get('work_total')" class="mt-2" />
                        </div>

                        <!-- Popis práce -->
                        <div class="mt-4">
                            <x-input-label for="descript" :value="__('Popis práce')" />
                            <textarea id="descript" name="descript" rows="5" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" required>{{ old('descript') }}</textarea>
                            <x-input-error :messages="$errors->get('descript')" class="mt-2" />
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

                        <!-- Typ -->
                        <div class="mt-4">
                            <x-input-label for="kind" :value="__('Typ')" />
                            <select id="kind" name="kind" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">
                                <option value="1" selected>Standardní</option>
                                <option value="2">Konzultace</option>
                                <option value="3">Vývoj</option>
                                <option value="4">Testování</option>
                            </select>
                            <x-input-error :messages="$errors->get('kind')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('requests.show', $ticketRequest) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-3">
                                {{ __('Zrušit') }}
                            </a>
                            <x-primary-button class="ml-4">
                                {{ __('Vytvořit report') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Timer Script -->
    <script src="{{ asset('js/timer.js') }}"></script>

    <script>
        // Automatické vypočítání celkového času
        document.addEventListener('DOMContentLoaded', function() {
            const workStart = document.getElementById('work_start');
            const workEnd = document.getElementById('work_end');
            const workTotal = document.getElementById('work_total');

            function calculateTotal() {
                if (workStart.value && workEnd.value) {
                    const start = new Date(workStart.value);
                    const end = new Date(workEnd.value);

                    if (end > start) {
                        const diff = Math.round((end - start) / (1000 * 60)); // Rozdíl v minutách
                        workTotal.value = diff;
                    }
                }
            }

            workStart.addEventListener('change', calculateTotal);
            workEnd.addEventListener('change', calculateTotal);
        });
    </script>
</x-app-layout>
