<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Upravit projektovou položku: ') }} {{ $projectItem->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('project_items.update', $projectItem) }}">
                        @csrf
                        @method('PUT')

                        <div>
                            <x-input-label for="id_project" :value="__('Projekt')" />
                            <select id="id_project" name="id_project" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" {{ $projectItem->id_project == $project->id ? 'selected' : '' }}>
                                        {{ $project->name }} ({{ $project->customer->name }})
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('id_project')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="name" :value="__('Název položky')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $projectItem->name)" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="state" :value="__('Stav')" />
                            <select id="state" name="state" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">
                                <option value="1" {{ $projectItem->state == 1 ? 'selected' : '' }}>Nový</option>
                                <option value="2" {{ $projectItem->state == 2 ? 'selected' : '' }}>V řešení</option>
                                <option value="3" {{ $projectItem->state == 3 ? 'selected' : '' }}>Čeká na zpětnou vazbu</option>
                                <option value="4" {{ $projectItem->state == 4 ? 'selected' : '' }}>Vyřešeno</option>
                                <option value="5" {{ $projectItem->state == 5 ? 'selected' : '' }}>Uzavřeno</option>
                            </select>
                            <x-input-error :messages="$errors->get('state')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="kind" :value="__('Typ')" />
                            <select id="kind" name="kind" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">
                                <option value="1" {{ $projectItem->kind == 1 ? 'selected' : '' }}>Standardní</option>
                                <option value="2" {{ $projectItem->kind == 2 ? 'selected' : '' }}>Systémová</option>
                                <option value="3" {{ $projectItem->kind == 3 ? 'selected' : '' }}>Prioritní</option>
                            </select>
                            <x-input-error :messages="$errors->get('kind')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('project_items.show', $projectItem) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-3">
                                {{ __('Zrušit') }}
                            </a>
                            <x-primary-button class="ml-4">
                                {{ __('Uložit změny') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
