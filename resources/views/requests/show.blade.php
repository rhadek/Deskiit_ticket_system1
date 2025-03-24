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

            <!-- Informace o požadavku -->
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

            <!-- Změna stavu -->
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
                                            @if ($message->id_user && $message->user)
                                                {{ $message->user->fname }} {{ $message->user->lname }} (zaměstnanec)
                                            @elseif ($message->id_custuser && $message->customerUser)
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
                                <div class="text-gray-700 whitespace-pre-wrap">{{ $message->message }}</div>
                            </div>
                        @empty
                            <p class="text-gray-500 text-center">Žádné zprávy k tomuto požadavku.</p>
                        @endforelse
                    </div>

                    <!-- Formulář pro přidání nové zprávy -->
                    <div class="mt-8">
                        <h4 class="text-md font-medium text-gray-900 mb-2">Přidat odpověď</h4>
                        <form action="{{ route('requests.add-message', $request) }}" method="POST">
                            @csrf
                            <div>
                                <textarea name="message" rows="4"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="Zadejte vaši zprávu..." required></textarea>
                                <x-input-error :messages="$errors->get('message')" class="mt-2" />
                            </div>
                            <div class="mt-4 flex justify-end">
                                <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                    {{ __('Odeslat zprávu') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Reporty práce -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="flex flex-wrap justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Reporty práce</h3>
                        <div class="flex space-x-2 mt-2 sm:mt-0">
                            <!-- Time Tracker Component -->
                            <x-time-tracker :requestId="$request->id" :requestName="$request->name" />

                            <!-- Manual report button -->
                            <a href="{{ route('request-reports.create', ['id_request' => $request->id]) }}"
                                class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                                {{ __('Přidat report manuálně') }}
                            </a>
                        </div>
                    </div>

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
                                                @if ($report->user)
                                                    {{ $report->user->fname }} {{ $report->user->lname }}
                                                @else
                                                    <span class="text-red-500">Uživatel smazán</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $report->work_start ? $report->work_start->format('d.m.Y H:i') : 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $report->work_end ? $report->work_end->format('d.m.Y H:i') : 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $report->work_total }} min
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">
                                                {{ $report->descript }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <a href="{{ route('request-reports.edit', $report) }}"
                                                    class="text-indigo-600 hover:text-indigo-900 mr-3">Upravit</a>
                                                <form action="{{ route('request-reports.destroy', $report) }}"
                                                    method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900"
                                                        onclick="return confirm('Opravdu chcete smazat tento report?')">Smazat</button>
                                                </form>
                                            </td>
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
