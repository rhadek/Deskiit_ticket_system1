<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detail projektu: ') }} {{ $project->name }}
            </h2>
            <div>
                <a href="{{ route('projects.edit', $project) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 mr-2">
                    {{ __('Upravit') }}
                </a>
                <a href="{{ route('projects.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
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

            <!-- Informace o projektu -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informace o projektu</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-semibold">Firma:</p>
                            <p class="text-gray-700">
                                <a href="{{ route('customers.show', $project->customer) }}" class="text-blue-600 hover:text-blue-900">
                                    {{ $project->customer->name }}
                                </a>
                            </p>
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
                        <a href="{{ route('project_items.create', ['id_project' => $project->id]) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                            {{ __('Přidat položku') }}
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
                                @forelse ($project->projectItems as $item)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->id }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <a href="{{ route('project_items.show', $item) }}" class="text-blue-600 hover:text-blue-900">
                                                {{ $item->name }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if ($item->state == 1)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Aktivní</span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Neaktivní</span>
                                            @endif
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
                                            <a href="{{ route('project_items.show', $item) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Detail</a>
                                            <a href="{{ route('project_items.edit', $item) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Upravit</a>

                                            <form action="{{ route('project_items.destroy', $item) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Opravdu chcete smazat tuto položku?')">Smazat</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            Projekt nemá žádné položky.
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
