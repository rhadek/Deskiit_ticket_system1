<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detail uživatele: ') }} {{ $customerUser->fname }} {{ $customerUser->lname }}
            </h2>
            <div>
                @if(auth()->user()->kind == 3)
                <a href="{{ route('customer_users.edit', $customerUser) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 mr-2">
                    {{ __('Upravit') }}
                </a>
                @endif
                <a href="{{ route('customer_users.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    {{ __('Zpět na seznam') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Informace o uživateli -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informace o uživateli</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-semibold">Firma:</p>
                            <p class="text-gray-700">
                                <a href="{{ route('customers.show', $customerUser->customer) }}" class="text-blue-600 hover:text-blue-900">
                                    {{ $customerUser->customer->name }}
                                </a>
                            </p>
                        </div>

                        <div>
                            <p class="text-sm font-semibold">Uživatelské jméno:</p>
                            <p class="text-gray-700">{{ $customerUser->username }}</p>
                        </div>

                        <div>
                            <p class="text-sm font-semibold">Jméno:</p>
                            <p class="text-gray-700">{{ $customerUser->fname }} {{ $customerUser->lname }}</p>
                        </div>

                        <div>
                            <p class="text-sm font-semibold">Email:</p>
                            <p class="text-gray-700">{{ $customerUser->email }}</p>
                        </div>

                        <div>
                            <p class="text-sm font-semibold">Telefon:</p>
                            <p class="text-gray-700">{{ $customerUser->telephone }}</p>
                        </div>

                        <div>
                            <p class="text-sm font-semibold">Stav:</p>
                            <p class="text-gray-700">
                                @if ($customerUser->state == 1)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Aktivní</span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Neaktivní</span>
                                @endif
                            </p>
                        </div>

                        <div>
                            <p class="text-sm font-semibold">Role:</p>
                            <p class="text-gray-700">
                                @if ($customerUser->kind == 1)
                                    Běžný uživatel
                                @elseif ($customerUser->kind == 2)
                                    Pokročilý uživatel
                                @elseif ($customerUser->kind == 3)
                                    Admin
                                @else
                                    Neznámá
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Záložky -->
            <div class="mb-6">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex">
                        <a href="#assigned" class="border-indigo-500 text-indigo-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm" onclick="showTab('assigned'); return false;">
                            Přiřazené projektové položky
                        </a>
                        <a href="#requests" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm ml-8" onclick="showTab('requests'); return false;">
                            Požadavky
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Tab obsah - Přiřazené projektové položky -->
            <div id="assigned-tab" class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Přiřazené projektové položky</h3>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Název</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Projekt</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stav</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Akce</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($customerUser->projectItems as $item)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->id }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $item->name }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $item->project->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @switch($item->state)
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
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('project_items.show', $item->id) }}" class="text-indigo-600 hover:text-indigo-900">Detail</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            Uživatel nemá přiřazené žádné projektové položky.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Tab obsah - Požadavky -->
            <div id="requests-tab" class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 hidden">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Požadavky</h3>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Název</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Projektová položka</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vytvořeno</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stav</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Akce</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($customerUser->requests as $request)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $request->id }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $request->name }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $request->projectItem->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $request->inserted }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if ($request->state == 1)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Nový</span>
                                            @elseif ($request->state == 2)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">V řešení</span>
                                            @elseif ($request->state == 3)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Čeká na zpětnou vazbu</span>
                                            @elseif ($request->state == 4)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">Vyřešeno</span>
                                            @elseif ($request->state == 5)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Uzavřeno</span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Neznámý</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href=" {{ route('requests.show', $request->id) }}" class="text-indigo-600 hover:text-indigo-900">Detail</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            Uživatel nemá žádné požadavky.
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

    <script>
        function showTab(tabName) {
            // Skryjeme všechny taby
            document.getElementById('assigned-tab').classList.add('hidden');
            document.getElementById('requests-tab').classList.add('hidden');

            // Zobrazíme požadovaný tab
            document.getElementById(tabName + '-tab').classList.remove('hidden');

            // Aktualizujeme aktivní záložku
            document.querySelectorAll('nav a').forEach(tab => {
                tab.classList.remove('border-indigo-500', 'text-indigo-600');
                tab.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
            });

            document.querySelector('nav a[href="#' + tabName + '"]').classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
            document.querySelector('nav a[href="#' + tabName + '"]').classList.add('border-indigo-500', 'text-indigo-600');
        }
    </script>
</x-app-layout>
