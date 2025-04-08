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
                    <form method="POST" action="{{ route('request-reports.store') }}">
                        @csrf

                        <div>
                            <x-input-label for="request" :value="__('Požadavek')" />
                            <x-text-input id="request" class="block mt-1 w-full bg-gray-100" type="text" value="{{ $ticketRequest->name }} ({{ $ticketRequest->projectItem->name }} - {{ $ticketRequest->projectItem->project->name }})" disabled />
                            <input type="hidden" name="id_request" value="{{ $ticketRequest->id }}">

                            @if(isset($trackerSession))
                                <input type="hidden" name="session_id" value="{{ $trackerSession->id }}">
                                <div class="mt-1 text-sm text-green-600">
                                    <span>Časy jsou předvyplněny z vašeho časovače.</span>
                                </div>
                            @endif
                        </div>

                        <div class="mt-4">
                            <x-input-label for="work_start" :value="__('Začátek práce')" />
                            <x-text-input id="work_start" class="block mt-1 w-full" type="datetime-local" name="work_start"
                                :value="old('work_start', isset($trackerSession) ? $trackerSession->start_time->format('Y-m-d\TH:i') : null)" required />
                            <x-input-error :messages="$errors->get('work_start')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="work_end" :value="__('Konec práce')" />
                            <x-text-input id="work_end" class="block mt-1 w-full" type="datetime-local" name="work_end"
                                :value="old('work_end', isset($trackerSession) ? $trackerSession->end_time->format('Y-m-d\TH:i') : null)" required />
                            <x-input-error :messages="$errors->get('work_end')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="work_total" :value="__('Celkový čas (minuty)')" />
                            <x-text-input id="work_total" class="block mt-1 w-full" type="number" name="work_total"
                                :value="old('work_total', isset($trackerSession) ? $trackerSession->total_minutes : null)" required min="1" />
                            <x-input-error :messages="$errors->get('work_total')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="descript" :value="__('Popis práce')" />
                            <textarea id="descript" name="descript" rows="5" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" required>{{ old('descript') }}</textarea>
                            <x-input-error :messages="$errors->get('descript')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="state" :value="__('Stav')" />
                            <select id="state" name="state" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">
                                <option value="1" selected>Aktivní</option>
                                <option value="0">Neaktivní</option>
                            </select>
                            <x-input-error :messages="$errors->get('state')" class="mt-2" />
                        </div>

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
