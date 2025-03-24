{{-- resources/views/components/time-tracker.blade.php --}}

@props(['requestId', 'requestName'])

<div id="time-tracker-component"
     class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4 mb-4"
     data-request-id="{{ $requestId }}"
     data-request-name="{{ $requestName }}">

    <div class="flex justify-between items-center mb-2">
        <h3 class="text-lg font-medium text-gray-900">Sledování času</h3>
        <div id="timeTracker_activeSession" class="hidden">
            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                Aktivní měření
            </span>
        </div>
    </div>

    <div id="timeTracker_requestInfo" class="mb-4 hidden">
        <div class="text-sm text-gray-600">
            <span>Měříte čas pro požadavek: </span>
            <span id="timeTracker_requestName" class="font-semibold"></span>
            <span id="timeTracker_differentRequestWarning" class="text-red-600 ml-2 hidden">
                (Pozor: Měříte čas pro jiný požadavek!)
            </span>
        </div>
    </div>

    <div class="flex justify-between items-center">
        <div id="timeTracker_timer" class="text-2xl font-bold text-gray-800">00:00:00</div>

        <div id="timeTracker_controls">
            <button id="timeTracker_startBtn"
                    class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 mr-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Start
            </button>

            <div id="timeTracker_activeControls" class="hidden">
                <button id="timeTracker_stopBtn"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 mr-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z" />
                    </svg>
                    Stop
                </button>

                <button id="timeTracker_cancelBtn"
                        class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Zrušit
                </button>
            </div>
        </div>
    </div>

    <!-- Report form modal (shows when stopping the timer) -->
    <div id="timeTracker_reportModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                            Uložit report práce
                        </h3>

                        <form id="timeTracker_reportForm" class="space-y-4">
                            @csrf
                            <!-- Hidden inputs -->
                            <input type="hidden" id="timeTracker_form_requestId" name="id_request">
                            <input type="hidden" id="timeTracker_form_work_start" name="work_start">
                            <input type="hidden" id="timeTracker_form_work_end" name="work_end">
                            <input type="hidden" id="timeTracker_form_work_total" name="work_total">

                            <!-- Description -->
                            <div>
                                <label for="timeTracker_form_descript" class="block text-sm font-medium text-gray-700">
                                    Popis práce
                                </label>
                                <textarea id="timeTracker_form_descript" name="descript" rows="3"
                                          class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                          required></textarea>
                            </div>

                            <!-- Type -->
                            <div>
                                <label for="timeTracker_form_kind" class="block text-sm font-medium text-gray-700">
                                    Typ
                                </label>
                                <select id="timeTracker_form_kind" name="kind"
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="1">Standardní</option>
                                    <option value="2">Konzultace</option>
                                    <option value="3">Vývoj</option>
                                    <option value="4">Testování</option>
                                </select>
                            </div>

                            <!-- Summary -->
                            <div class="bg-gray-50 p-3 rounded border">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Shrnutí časů:</h4>
                                <div class="grid grid-cols-2 gap-2 text-sm">
                                    <div>
                                        <span class="text-gray-500">Začátek:</span>
                                        <span id="timeTracker_summary_start" class="ml-1 font-medium"></span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500">Konec:</span>
                                        <span id="timeTracker_summary_end" class="ml-1 font-medium"></span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500">Celkový čas:</span>
                                        <span id="timeTracker_summary_total" class="ml-1 font-medium"></span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500">Minuty:</span>
                                        <span id="timeTracker_summary_minutes" class="ml-1 font-medium"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- State (hidden) -->
                            <input type="hidden" name="state" value="1">
                        </form>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" id="timeTracker_saveReportBtn"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                    Uložit report
                </button>
                <button type="button" id="timeTracker_cancelReportBtn"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Zrušit
                </button>
            </div>
        </div>
    </div>
</div>
