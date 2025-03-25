<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Požadavek: ') }} {{ $request->name }}
            </h2>
            <div>
                @if (auth()->user()->kind == 3)
                    <a href="{{ route('requests.edit', $request) }}"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 mr-2">
                        {{ __('Upravit') }}
                    </a>
                @endif
                <a href="{{ route('requests.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    {{ __('Zpět na seznam') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informace o požadavku</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-semibold">Projektová položka:</p>
                            <p class="text-gray-700">
                                @if ($request->projectItem)
                                    <a href="{{ route('project_items.show', $request->projectItem) }}"
                                        class="text-blue-600 hover:text-blue-900">
                                        {{ $request->projectItem->name }}
                                    </a>
                                @else
                                    <span class="text-red-500">Položka byla smazána</span>
                                @endif
                            </p>
                        </div>

                        <div>
                            <p class="text-sm font-semibold">Projekt:</p>
                            <p class="text-gray-700">
                                @if ($request->projectItem && $request->projectItem->project)
                                    <a href="{{ route('projects.show', $request->projectItem->project) }}"
                                        class="text-blue-600 hover:text-blue-900">
                                        {{ $request->projectItem->project->name }}
                                    </a>
                                @else
                                    <span class="text-red-500">Projekt byl smazán</span>
                                @endif
                            </p>
                        </div>

                        <div>
                            <p class="text-sm font-semibold">Firma:</p>
                            <p class="text-gray-700">
                                @if ($request->projectItem && $request->projectItem->project && $request->projectItem->project->customer)
                                    <a href="{{ route('customers.show', $request->projectItem->project->customer) }}"
                                        class="text-blue-600 hover:text-blue-900">
                                        {{ $request->projectItem->project->customer->name }}
                                    </a>
                                @else
                                    <span class="text-red-500">Firma byla smazána</span>
                                @endif
                            </p>
                        </div>

                        <div>
                            <p class="text-sm font-semibold">Zadavatel:</p>
                            <p class="text-gray-700">
                                @if ($request->customerUser)
                                    <a href="{{ route('customer_users.show', $request->customerUser) }}"
                                        class="text-blue-600 hover:text-blue-900">
                                        {{ $request->customerUser->fname }} {{ $request->customerUser->lname }}
                                    </a>
                                @else
                                    <span class="text-red-500">Zadavatel byl smazán</span>
                                @endif
                            </p>
                        </div>

                        <div>
                            <p class="text-sm font-semibold">Vytvořeno:</p>
                            <p class="text-gray-700">{{ $request->inserted->format('d.m.Y H:i') }}</p>
                        </div>

                        <div>
                            <p class="text-sm font-semibold">Stav:</p>
                            <p class="text-gray-700">
                                @if ($request->state == 1)
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Nový</span>
                                @elseif ($request->state == 2)
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">V
                                        řešení</span>
                                @elseif ($request->state == 3)
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">Čeká
                                        na zpětnou vazbu</span>
                                @elseif ($request->state == 4)
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Vyřešeno</span>
                                @elseif ($request->state == 5)
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Uzavřeno</span>
                                @else
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Neznámý</span>
                                @endif
                            </p>
                        </div>

                        <div>
                            <p class="text-sm font-semibold">Typ:</p>
                            <p class="text-gray-700">
                                @if ($request->kind == 1)
                                    Standardní
                                @elseif ($request->kind == 2)
                                    Chyba
                                @elseif ($request->kind == 3)
                                    Prioritní
                                @else
                                    Neznámý
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Změna stavu</h3>

                    <form action="{{ route('requests.change-state', $request) }}" method="POST"
                        class="flex items-center space-x-4">
                        @csrf
                        @method('PATCH')

                        <select name="state"
                            class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="1" {{ $request->state == 1 ? 'selected' : '' }}>Nový</option>
                            <option value="2" {{ $request->state == 2 ? 'selected' : '' }}>V řešení</option>
                            <option value="3" {{ $request->state == 3 ? 'selected' : '' }}>Čeká na zpětnou vazbu
                            </option>
                            <option value="4" {{ $request->state == 4 ? 'selected' : '' }}>Vyřešeno</option>
                            <option value="5" {{ $request->state == 5 ? 'selected' : '' }}>Uzavřeno</option>
                        </select>

                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                            {{ __('Změnit stav') }}
                        </button>
                    </form>
                </div>
            </div>

            <!-- Přílohy požadavku -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Přílohy požadavku</h3>

                    <!-- Display media -->
                    @if($request->media && $request->media->count() > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-6">
                            @foreach($request->media as $file)
                                <div class="border rounded-lg overflow-hidden bg-white shadow-sm hover:shadow-md transition">
                                    <div class="p-3 border-b bg-gray-50">
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm font-medium truncate" title="{{ $file->name }}">
                                                {{ \Illuminate\Support\Str::limit($file->name, 20) }}
                                            </span>
                                            @if(auth()->user()->kind == 3)
                                                <form action="{{ route('media.destroy', $file->id) }}" method="POST" class="inline" onsubmit="return confirm('Opravdu chcete smazat tento soubor?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <input type="hidden" name="redirect_url" value="{{ url()->current() }}">
                                                    <button type="submit" class="text-red-600 hover:text-red-800">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="p-4 flex flex-col items-center">
                                        <div class="mb-3 h-16 flex items-center justify-center">
                                            @if($file->kind == 1)
                                                {{-- Image --}}
                                                <img src="{{ route('media.show', $file->id) }}" alt="{{ $file->name }}" class="max-h-16 max-w-full object-contain">
                                            @elseif($file->kind == 2)
                                                {{-- PDF --}}
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                </svg>
                                            @elseif($file->kind == 3)
                                                {{-- Office Document --}}
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                            @elseif($file->kind == 4)
                                                {{-- Text file --}}
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                            @elseif($file->kind == 5)
                                                {{-- Archive --}}
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                                </svg>
                                            @else
                                                {{-- Other --}}
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                </svg>
                                            @endif
                                        </div>

                                        <div class="w-full">
                                            <a href="{{ route('media.download', $file->id) }}" class="inline-flex justify-center w-full items-center px-3 py-1.5 bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-md text-xs font-medium text-gray-700 transition">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                </svg>
                                                Stáhnout
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center p-6 text-gray-500 mb-6">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p>Žádné soubory nebyly nahrány</p>
                        </div>
                    @endif

                    <!-- Form to add new media directly to the request -->
                    @if($request->state != 5) {{-- Don't allow uploads if request is closed --}}
                        <div>
                            <form action="{{ route('media.store') }}" method="POST" enctype="multipart/form-data" class="border-2 border-dashed border-gray-300 rounded-lg p-4">
                                @csrf
                                <input type="hidden" name="entity_type" value="request">
                                <input type="hidden" name="entity_id" value="{{ $request->id }}">
                                <input type="hidden" name="redirect_url" value="{{ url()->current() }}">

                                <div class="mb-4">
                                    <label for="request_file" class="block text-sm font-medium text-gray-700 mb-2">Přidat přílohu k požadavku</label>
                                    <input type="file" id="request_file" name="file" class="block w-full text-sm text-gray-500
                                        file:mr-4 file:py-2 file:px-4
                                        file:rounded-md file:border-0
                                        file:text-sm file:font-semibold
                                        file:bg-indigo-50 file:text-indigo-700
                                        hover:file:bg-indigo-100" required>
                                    <p class="mt-1 text-xs text-gray-500">Maximální velikost souboru: 10MB</p>
                                </div>

                                <div class="flex justify-end">
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                        Nahrát soubor
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Zprávy -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Zprávy</h3>

                    <div class="space-y-6">
                        @forelse ($request->messages as $message)
                            <div
                                class="border rounded-lg p-4 {{ $message->id_user ? 'bg-blue-50 border-blue-200' : 'bg-green-50 border-green-200' }}">
                                <div class="flex justify-between mb-2">
                                    <div>
                                        <span class="font-medium">
                                            @if ($message->id_user && isset($message->user))
                                                {{ $message->user->fname }} {{ $message->user->lname }} (zaměstnanec)
                                            @elseif ($message->id_custuser && isset($message->customerUser))
                                                {{ $message->customerUser->fname }}
                                                {{ $message->customerUser->lname }} (zákazník)
                                            @else
                                                Neznámý uživatel
                                            @endif
                                        </span>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $message->inserted->format('d.m.Y H:i') }}
                                    </div>
                                </div>
                                <div class="text-gray-700 whitespace-pre-wrap mb-3">{{ $message->message }}</div>

                                <!-- Display attachments for this message -->
                                @if(isset($message->media) && $message->media->count() > 0)
                                    <div class="pt-3 border-t border-gray-200">
                                        <p class="text-sm font-medium text-gray-700 mb-2">Přílohy:</p>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($message->media as $mediaItem)
                                                <a href="{{ route('media.download', $mediaItem->id) }}"
                                                   class="inline-flex items-center px-2 py-1 bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-md text-xs font-medium text-gray-700 transition">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                    </svg>
                                                    {{ $mediaItem->name }}
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <p class="text-gray-500 text-center">Žádné zprávy k tomuto požadavku.</p>
                        @endforelse
                    </div>

                    <!-- Formulář pro přidání nové zprávy -->
                    @if($request->state != 5) {{-- Don't allow new messages if request is closed --}}
                        <div class="mt-8">
                            <h4 class="text-md font-medium text-gray-900 mb-2">Přidat odpověď</h4>
                            <form action="{{ route('requests.add-message', $request) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div>
                                    <textarea name="message" rows="4"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="Zadejte vaši zprávu..." required></textarea>
                                    <x-input-error :messages="$errors->get('message')" class="mt-2" />
                                </div>

                                <!-- Přidání souboru -->
                                <div class="mt-4">
                                    <label for="file" class="block text-sm font-medium text-gray-700 mb-2">
                                        Příloha (volitelně)
                                    </label>
                                    <input type="file" id="file" name="file" class="block w-full text-sm text-gray-500
                                        file:mr-4 file:py-2 file:px-4
                                        file:rounded-md file:border-0
                                        file:text-sm file:font-semibold
                                        file:bg-indigo-50 file:text-indigo-700
                                        hover:file:bg-indigo-100">
                                    <p class="mt-1 text-xs text-gray-500">
                                        Maximální velikost souboru: 10MB. Podporované typy: obrázky, PDF, dokumenty, ZIP.
                                    </p>
                                </div>

                                <div class="mt-4 flex justify-end">
                                    <button type="submit"
                                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                        {{ __('Odeslat zprávu') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    @else
                        <div class="mt-8 p-4 bg-gray-100 rounded">
                            <p class="text-gray-700">Tento požadavek je uzavřen a nelze k němu přidat další zprávy.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sekce pro reporty práce -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Reporty práce</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <div class="mb-2 text-sm font-medium text-gray-700">Automatické měření času</div>
                                <x-time-tracker :requestId="$request->id" :requestName="$request->name" />
                            </div>

                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 flex flex-col justify-between">
                                <div class="mb-2 text-sm font-medium text-gray-700">Manuální přidání reportu</div>
                                <p class="text-sm text-gray-500 mb-4">Zapomněl si trackovat? Přidej report manuálně.</p>
                                <div>
                                    <a href="{{ route('request-reports.create', ['id_request' => $request->id]) }}"
                                        class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                                        {{ __('Přidat report manuálně') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Výpis reportů práce -->
                    @if (count($request->reports) > 0)
                        <div class="overflow-x-auto mb-6">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Pracovník</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Od</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Do</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Celkem</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Popis</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Akce</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($request->reports as $report)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if (isset($report->user))
                                                    {{ $report->user->fname }} {{ $report->user->lname }}
                                                @else
                                                    <span class="text-red-500">Uživatel smazán</span>
                                                @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500 text-center mb-4">Žádné reporty práce k tomuto požadavku.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
