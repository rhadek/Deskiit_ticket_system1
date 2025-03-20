<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detail firmy: ') }} {{ $customer->name }}
            </h2>
            <div>
                <a href="{{ route('customers.edit', $customer) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 mr-2">
                    {{ __('Upravit') }}
                </a>
                <a href="{{ route('customers.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
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

            <!-- Informace o firmě -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informace o firmě</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-semibold">ID:</p>
                            <p class="text-gray-700">{{ $customer->id }}</p>
                        </div>

                        <div>
                            <p class="text-sm font-semibold">Název:</p>
                            <p class="text-gray-700">{{ $customer->name }}</p>
                        </div>

                        <div>
                            <p class="text-sm font-semibold">IČ:</p>
                            <p class="text-gray-700">{{ $customer->ic }}</p>
                        </div>

                        <div>
                            <p class="text-sm font-semibold">Stav:</p>
                            <p class="text-gray-700">
                                @if ($customer->state == 1)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Aktivní</span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Neaktivní</span>
                                @endif
                            </p>
                        </div>

                        <div>
                            <p class="text-sm font-semibold">Typ:</p>
                            <p class="text-gray-700">
                                @if ($customer->kind == 1)
                                    Standardní
                                @elseif ($customer->kind == 2)
                                    VIP
                                @elseif ($customer->kind == 3)
                                    Korporátní
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
                            Uživatelé firmy
                        </a>
                        <a href="#projects" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm ml-8" onclick="showTab('projects'); return false;">
                            Projekty
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Tab obsah - Uživatelé firmy -->
            <div id="users-tab" class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Uživatelé firmy</h3>
                        <a href="{{ route('customer_users.create', ['id_customer' => $customer->id]) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                            {{ __('Přidat uživatele') }}
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jméno</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Telefon</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stav</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Akce</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($customer->customerUsers as $user)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->id }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <a href="{{ route('customer_users.show', $user) }}" class="text-blue-600 hover:text-blue-900">
                                                <div class="text-sm font-medium text-gray-900">{{ $user->fname }} {{ $user->lname }}</div>
                                                <div class="text-sm text-gray-500">{{ $user->username }}</div>
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->email }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->telephone }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if ($user->state == 1)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Aktivní</span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Neaktivní</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('customer_users.edit', $user) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Upravit</a>

                                            <form action="{{ route('customer_users.destroy', $user) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Opravdu chcete smazat tohoto uživatele?')">Smazat</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            Firma nemá žádné uživatele.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Tab obsah - Projekty -->
            <div id="projects-tab" class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 hidden">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Projekty</h3>
                        <a href="{{ route('projects.create', ['id_customer' => $customer->id]) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                            {{ __('Vytvořit projekt') }}
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Název</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stav</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Typ</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Akce</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($customer->projects as $project)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $project->id }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <a href="{{ route('projects.show', $project) }}" class="text-blue-600 hover:text-blue-900">
                                                <div class="text-sm font-medium text-gray-900">{{ $project->name }}</div>
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if ($project->state == 1)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Aktivní</span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Neaktivní</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $project->kind }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('projects.show', $project) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Zobrazit</a>
                                            <a href="{{ route('projects.edit', $project) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Upravit</a>

                                            <form action="{{ route('projects.destroy', $project) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Opravdu chcete smazat tento projekt?')">Smazat</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            Firma nemá žádné projekty.
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
            document.getElementById('projects-tab').classList.add('hidden');

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
