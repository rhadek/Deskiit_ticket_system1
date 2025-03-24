<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detail položky: ') }} {{ $projectItem->name }}
            </h2>
            <div>
                @if(auth()->user()->kind == 3)
                <a href="{{ route('project_items.edit', $projectItem) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 mr-2">
                    {{ __('Upravit') }}
                </a>
                @endif
                <a href="{{ route('project_items.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
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

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            @if(session('info'))
                <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-4">
                    {{ session('info') }}
                </div>
            @endif

            <!-- Informace o položce -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informace o položce</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-semibold">Projekt:</p>
                            <p class="text-gray-700">
                                <a href="{{ route('projects.show', $projectItem->project) }}" class="text-blue-600 hover:text-blue-900">
                                    {{ $projectItem->project->name }}
                                </a>
                            </p>
                        </div>

                        <div>
                            <p class="text-sm font-semibold">Firma:</p>
                            <p class="text-gray-700">
                                <a href="{{ route('customers.show', $projectItem->project->customer) }}" class="text-blue-600 hover:text-blue-900">
                                    {{ $projectItem->project->customer->name }}
                                </a>
                            </p>
                        </div>

                        <div>
                            <p class="text-sm font-semibold">Název:</p>
                            <p class="text-gray-700">{{ $projectItem->name }}</p>
                        </div>

                        <!-- Tento kód vložte do vašeho project_items/show.blade.php v části, kde se zobrazuje stav položky -->

                        <div>
                            <p class="text-sm font-semibold">Stav:</p>
                            <p class="text-gray-700">
                                @php
                                    $statusColor = match ((int)$projectItem->state) {
                                        1 => 'bg-blue-100 text-blue-800',
                                        2 => 'bg-yellow-100 text-yellow-800',
                                        3 => 'bg-purple-100 text-purple-800',
                                        4 => 'bg-green-100 text-green-800',
                                        5 => 'bg-gray-100 text-gray-800',
                                        default => 'bg-gray-100 text-gray-800'
                                    };
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColor }}">
                                    @switch($projectItem->state)
                                        @case(1)
                                            Nový
                                            @break
                                        @case(2)
                                            V řešení
                                            @break
                                        @case(3)
                                            Čeká na zpětnou vazbu
                                            @break
                                        @case(4)
                                            Vyřešeno
                                            @break
                                        @case(5)
                                            Uzavřeno
                                            @break
                                        @default
                                            Neznámý stav
                                    @endswitch
                                </span>
                            </p>
                        </div>

                        <div>
                            <p class="text-sm font-semibold">Typ:</p>
                            <p class="text-gray-700">
                                @if ($projectItem->kind == 1)
                                    Standardní
                                @elseif ($projectItem->kind == 2)
                                    Systémová
                                @elseif ($projectItem->kind == 3)
                                    Prioritní
                                @else
                                    Neznámý
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
                        <a href="#users" class="border-indigo-500 text-indigo-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm" onclick="showTab('users'); return false;">
                            Přiřazení uživatelé
                        </a>
                        <a href="#customer-users" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm ml-8" onclick="showTab('customer-users'); return false;">
                            Přiřazení zákazničtí uživatelé
                        </a>
                        <a href="#requests" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm ml-8" onclick="showTab('requests'); return false;">
                            Požadavky
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Tab obsah - Přiřazení uživatelé -->
            <div id="users-tab" class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Přiřazení uživatelé (zaměstnanci)</h3>
                        @if(auth()->user()->kind == 3)
                        <div>
                            <form action="{{ route('project_items.assign.user', $projectItem) }}" method="POST" class="flex items-center">
                                @csrf
                                <select name="user_id" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mr-2">
                                    <option value="">Vyberte uživatele</option>
                                    @foreach($availableUsers as $user)
                                        <option value="{{ $user->id }}">{{ $user->fname }} {{ $user->lname }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                                    {{ __('Přiřadit') }}
                                </button>
                            </form>
                        </div>
                        @endif
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jméno</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Telefon</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                    @if(auth()->user()->kind == 3)
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Akce</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($projectItem->users as $user)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->id }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $user->fname }} {{ $user->lname }}</div>
                                            <div class="text-sm text-gray-500">{{ $user->username }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->email }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->telephone }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if ($user->kind == 1)
                                                Běžný uživatel
                                            @elseif ($user->kind == 2)
                                                Pokročilý uživatel
                                            @elseif ($user->kind == 3)
                                                Admin
                                            @endif
                                        </td>
                                        @if(auth()->user()->kind == 3)
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <form action="{{ route('project_items.remove.user', [$projectItem, $user]) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Opravdu chcete odebrat tohoto uživatele?')">Odebrat</button>
                                            </form>
                                        </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ auth()->user()->kind == 3 ? 6 : 5 }}" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            K této položce nejsou přiřazeni žádní uživatelé.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Tab obsah - Přiřazení zákazničtí uživatelé -->
            <div id="customer-users-tab" class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 hidden">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Přiřazení zákazničtí uživatelé</h3>
                        @if(auth()->user()->kind == 3)
                        <div>
                            <form action="{{ route('project_items.assign.customer-user', $projectItem) }}" method="POST" class="flex items-center">
                                @csrf
                                <select name="customer_user_id" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mr-2">
                                    <option value="">Vyberte zákaznického uživatele</option>
                                    @foreach($availableCustomerUsers as $customerUser)
                                        <option value="{{ $customerUser->id }}">{{ $customerUser->fname }} {{ $customerUser->lname }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                                    {{ __('Přiřadit') }}
                                </button>
                            </form>
                        </div>
                        @endif
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jméno</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Telefon</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                    @if(auth()->user()->kind == 3)
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Akce</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($projectItem->customerUsers as $customerUser)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $customerUser->id }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <a href="{{ route('customer_users.show', $customerUser) }}" class="text-blue-600 hover:text-blue-900">
                                                <div class="text-sm font-medium text-gray-900">{{ $customerUser->fname }} {{ $customerUser->lname }}</div>
                                                <div class="text-sm text-gray-500">{{ $customerUser->username }}</div>
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $customerUser->email }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $customerUser->telephone }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if ($customerUser->kind == 1)
                                                Běžný uživatel
                                            @elseif ($customerUser->kind == 2)
                                                Pokročilý uživatel
                                            @elseif ($customerUser->kind == 3)
                                                Admin
                                            @endif
                                        </td>
                                        @if(auth()->user()->kind == 3)
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <form action="{{ route('project_items.remove.customer-user', [$projectItem, $customerUser]) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Opravdu chcete odebrat tohoto zákaznického uživatele?')">Odebrat</button>
                                            </form>
                                        </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ auth()->user()->kind == 3 ? 6 : 5 }}" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            K této položce nejsou přiřazeni žádní zákazničtí uživatelé.
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
                        <a href="{{ route('project_items.create', ['id_project' => $projectItem->id]) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                            {{ __('Nový požadavek') }}
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Název</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vytvořeno</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Zákazník</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stav</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Akce</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($projectItem->requests as $request)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $request->id }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <a href="{{ route('requests.show', $request->id) }}" class="text-blue-600 hover:text-blue-900">
                                                {{ $request->name }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $request->inserted }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <a href="{{ route('customer_users.show', $request->customerUser) }}" class="text-blue-600 hover:text-blue-900">
                                                {{ $request->customerUser->fname }} {{ $request->customerUser->lname }}
                                            </a>
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
                                            <a href="{{ route('requests.show', $request->id) }}" class="text-indigo-600 hover:text-indigo-900">Detail</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            K této položce nejsou přiřazeny žádné požadavky.
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
            document.getElementById('users-tab').classList.add('hidden');
            document.getElementById('customer-users-tab').classList.add('hidden');
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
