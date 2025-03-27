<x-customer-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detail projektu: ') }} {{ $project->name }}
            </h2>
            <div>
                @if(Auth::guard('customer')->user()->kind == 3)
                    <a href="{{ route('customer.projects.edit', $project) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 mr-2">
                        {{ __('Upravit') }}
                    </a>
                @endif
                <a href="{{ route('customer.projects.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    {{ __('Zpět na seznam') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(Auth::guard('customer')->user()->kind == 3)
                <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-4">
                    <p class="font-bold">Administrátorský režim</p>
                    <p>Jako administrátor můžete spravovat tento projekt a jeho položky.</p>
                </div>
            @endif

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

            <!-- Informace o projektu -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informace o projektu</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-semibold">Firma:</p>
                            <p class="text-gray-700">{{ $project->customer->name }}</p>
                        </div>

                        <div>
                            <p class="text-sm font-semibold">Název:</p>
                            <p class="text-gray-700">{{ $project->name }}</p>
                        </div>

                        <div>
                            <p class="text-sm font-semibold">Stav:</p>
                            <p class="text-gray-700">
                                @if ($project->state == 1)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Aktivní</span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Neaktivní</span>
                                @endif
                            </p>
                        </div>

                        <div>
                            <p class="text-sm font-semibold">Typ:</p>
                            <p class="text-gray-700">
                                @if ($project->kind == 1)
                                    Standardní
                                @elseif ($project->kind == 2)
                                    VIP
                                @elseif ($project->kind == 3)
                                    Korporátní
                                @else
                                    Neznámý
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Projektové položky -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Projektové položky</h3>
                        @if(Auth::guard('customer')->user()->kind == 3)
                            <a href="{{ route('customer.project_items.create', ['id_project' => $project->id]) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                                {{ __('Přidat položku') }}
                            </a>
                        @endif
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Název</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stav</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Typ</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Akce</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($projectItems as $item)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                <a href="{{ route('customer.project_items.show', $item) }}" class="text-blue-600 hover:text-blue-900">
                                                    {{ $item->name }}
                                                </a>
                                            </div>
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
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if ($item->kind == 1)
                                                Standardní
                                            @elseif ($item->kind == 2)
                                                Systémová
                                            @elseif ($item->kind == 3)
                                                Prioritní
                                            @else
                                                Neznámý
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('customer.project_items.show', $item) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Detail</a>
                                            <a href="{{ route('customer.requests.create', ['id_projectitem' => $item->id]) }}" class="text-green-600 hover:text-green-900 mr-3">Vytvořit požadavek</a>

                                            @if(Auth::guard('customer')->user()->kind == 3)
                                                <a href="{{ route('customer.project_items.edit', $item) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Upravit</a>

                                                @if(Auth::guard('customer')->user()->kind == 3)
                                                    <a href="{{ route('customer.project_items.assign_users', $item) }}" class="text-purple-600 hover:text-purple-900 mr-3">Přiřadit uživatele</a>
                                                @endif

                                                <form action="{{ route('customer.project_items.destroy', $item) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Opravdu chcete smazat tuto položku?')">Smazat</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            Projekt nemá žádné položky, ke kterým máte přístup.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $projectItems->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-customer-layout>
