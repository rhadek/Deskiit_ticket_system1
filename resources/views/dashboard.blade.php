<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Uvítací blok -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h2 class="text-2xl font-bold mb-2">Dobrý den, {{ Auth::user()->fname }}!</h2>
                    <p class="text-gray-600">Vítejte v přehledu vašich projektů a požadavků.</p>
                </div>
            </div>

            <!-- Hlavní obsah - dva sloupce -->
            <div class="flex flex-col md:flex-row gap-6">
                <!-- Levý sloupec (projekty) - zůstává stejný -->
                <div class="md:w-1/4">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-4">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-semibold text-gray-800">Projekty</h3>
                                @if(Auth::user()->kind == 3)
                                <a href="{{ route('projects.create') }}" class="text-blue-600 hover:text-blue-800 text-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                                    </svg>
                                </a>
                                @endif
                            </div>
                            <div class="space-y-2">
                                <a href="{{ route('dashboard') }}" class="block p-2 {{ !request('project_id') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'hover:bg-gray-100' }} rounded transition">
                                    <span class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                                        </svg>
                                        Všechny projekty
                                    </span>
                                </a>

                                @forelse ($projects as $project)
                                    <a href="{{ route('dashboard', ['project_id' => $project->id]) }}"
                                       class="block p-2 {{ request('project_id') == $project->id ? 'bg-indigo-50 text-indigo-700 font-medium' : 'hover:bg-gray-100' }} rounded transition">
                                        {{ $project->name }}
                                        <span class="text-xs {{ request('project_id') == $project->id ? 'text-indigo-500' : 'text-gray-500' }}">
                                            ({{ $project->customer->name }})
                                        </span>
                                    </a>
                                @empty
                                    <p class="text-gray-500 text-center">Žádné projekty k zobrazení</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pravý sloupec (hlavní obsah - projektové položky a požadavky) -->
                <div class="md:w-3/4">
                    <!-- Projektové položky -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-semibold text-gray-800">
                                    @if($selectedProject)
                                        Projektové položky: {{ $selectedProject->name }}
                                    @else
                                        Projektové položky
                                    @endif
                                </h3>
                                @if(Auth::user()->kind == 3)
                                <a href="{{ route('project_items.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                                    {{ __('Přidat položku') }}
                                </a>
                                @endif
                            </div>

                            <div class="mb-6">
                                <h4 class="font-medium text-gray-700 mb-2">Podle stavu:</h4>
                                @php
                                    // Seřazení kategorií od 1 do 5
                                    $sorted_states = collect([1, 2, 3, 4, 5])
                                        ->filter(function($state) use ($grouped_by_state) {
                                            return isset($grouped_by_state[$state]);
                                        })
                                        ->values(); // Důležité pro získání sekvenčních indexů

                                    // Celkový počet stavů
                                    $total_states = $sorted_states->count();
                                @endphp

                                <div class="relative overflow-hidden">
                                    <div class="overflow-x-auto snap-x scrollbar pb-4">
                                        <div class="flex overflow-x-auto space-x-4 w-full">
                                            @foreach($sorted_states as $i => $state)
                                            <div class="flex-none w-1/2 p-2 snap-start">
                                                <div class="border rounded-lg p-4 h-full">
                                                    <h5 class="font-medium text-gray-700 mb-2">
                                                        {{ $state_names[$state] ?? 'Status ' . $state }}
                                                        <span class="text-sm text-gray-500">({{ count($grouped_by_state[$state]) }})</span>
                                                    </h5>
                                                    <div class="space-y-2 max-h-80 overflow-y-auto">
                                                        @foreach($grouped_by_state[$state] as $item)
                                                        <div class="border-b pb-2 last:border-b-0">
                                                            <a href="{{ route('dashboard', ['project_id' => $selectedProjectId, 'project_item_id' => $item->id]) }}" class="text-blue-600 hover:text-blue-900">
                                                                {{ $item->name }}
                                                            </a>
                                                            <div class="text-xs text-gray-500">
                                                                {{ $item->project->name }}
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach

                                            @if($total_states == 0)
                                                <div class="w-full text-center text-gray-500">
                                                    Nebyly nalezeny žádné projektové položky.
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Požadavky projektových položek -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-semibold text-gray-800">
                                    @if($selectedProjectItem)
                                        Požadavky položky: {{ $selectedProjectItem->name }}
                                    @elseif($selectedProject)
                                        Požadavky projektu: {{ $selectedProject->name }}
                                    @else
                                        Požadavky projektových položek
                                    @endif
                                </h3>
                                @if(Auth::user()->kind == 3)
                                <a href="{{ route('requests.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                                    {{ __('Nový požadavek') }}
                                </a>
                                @endif
                            </div>

                            <div class="mb-6">
                                <h4 class="font-medium text-gray-700 mb-2">Podle stavu:</h4>
                                @php
                                    // Získáme požadavky pro projektové položky
                                    $item_requests = collect();
                                    if (isset($selectedProjectItem) && $selectedProjectItem) {
                                        // Pokud máme vybranou projektovou položku, získáme její požadavky
                                        $item_requests = $selectedProjectItem->requests;
                                    } elseif ($selectedProjectId) {
                                        // Pokud máme vybraný projekt, získáme požadavky všech jeho položek
                                        $project_items->where('id_project', $selectedProjectId)->each(function ($item) use (&$item_requests) {
                                            if ($item->requests) {
                                                $item_requests = $item_requests->concat($item->requests);
                                            }
                                        });
                                    }

                                    // Seskupíme požadavky podle stavu
                                    $requests_by_state = $item_requests->groupBy('state');

                                    // Seřazení kategorií od 1 do 5
                                    $request_states = collect([1, 2, 3, 4, 5])
                                        ->filter(function($state) use ($requests_by_state) {
                                            return isset($requests_by_state[$state]);
                                        })
                                        ->values(); // Důležité pro získání sekvenčních indexů

                                    // Celkový počet stavů
                                    $total_request_states = $request_states->count();
                                @endphp

                                <div class="relative overflow-hidden">
                                    <div class="overflow-x-auto snap-x scrollbar pb-4">
                                        <div class="flex overflow-x-auto space-x-4 w-full">
                                            @foreach($request_states as $state)
                                            <div class="flex-none w-1/2 p-2 snap-start">
                                                <div class="border rounded-lg p-4 h-full">
                                                    <h5 class="font-medium text-gray-700 mb-2">
                                                        {{ $state_names[$state] ?? 'Status ' . $state }}
                                                        <span class="text-sm text-gray-500">({{ count($requests_by_state[$state]) }})</span>
                                                    </h5>
                                                    <div class="space-y-2 max-h-80 overflow-y-auto">
                                                        @foreach($requests_by_state[$state] as $req)
                                                            <div class="border-b pb-2 last:border-b-0">
                                                                <a href="{{ route('requests.show', $req) }}" class="text-blue-600 hover:text-blue-900">
                                                                    {{ $req->name }}
                                                                </a>
                                                                <div class="text-xs text-gray-500">
                                                                    {{ $req->projectItem->name }} / {{ $req->customerUser->fname }} {{ $req->customerUser->lname }}
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach



                                            @if($total_request_states == 0)
                                                <div class="w-full text-center text-gray-500 p-4">
                                                    @if($selectedProjectItem)
                                                        Nebyly nalezeny žádné požadavky pro tuto projektovou položku.
                                                    @elseif($selectedProject)
                                                        Nebyly nalezeny žádné požadavky pro tento projekt. Vyberte projektovou položku pro zobrazení požadavků.
                                                    @else
                                                        Nebyly nalezeny žádné požadavky. Vyberte projekt a projektovou položku pro zobrazení požadavků.
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sekce nejnovějších požadavků - vždy dole -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Nejnovější požadavky</h3>
                        <a href="{{ route('requests.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">
                            Zobrazit vše
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Název</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Projekt</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Zadavatel</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stav</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Akce</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($recent_requests as $request)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <a href="{{ route('requests.show', $request) }}" class="text-blue-600 hover:text-blue-900">
                                                {{ $request->name }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $request->projectItem->project->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $request->customerUser->fname }} {{ $request->customerUser->lname }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @switch($request->state)
                                                @case(1)
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Nový</span>
                                                    @break
                                                @case(2)
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">V řešení</span>
                                                    @break
                                                @case(3)
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">Čeká na zpětnou vazbu</span>
                                                    @break
                                                @case(4)
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Vyřešeno</span>
                                                    @break
                                                @case(5)
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Uzavřeno</span>
                                                    @break
                                                @default
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Neznámý</span>
                                            @endswitch
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('requests.show', $request) }}" class="text-indigo-600 hover:text-indigo-900">Detail</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            Žádné požadavky
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
