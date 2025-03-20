<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Vytvořit novou projektovou položku') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('project_items.store') }}">
                        @csrf

                        <!-- Projekt -->
                        <div>
                            <x-input-label for="id_project" :value="__('Projekt')" />
                            <select id="id_project" name="id_project" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" {{ $selectedProject && $selectedProject->id == $project->id ? 'selected' : '' }}>
                                        {{ $project->name }} ({{ $project->customer->name }})
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('id_project')" class="mt-2" />
                        </div>

                        <!-- Název položky -->
                        <div class="mt-4">
                            <x-input-label for="name" :value="__('Název položky')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Stav -->
                        <div class="mt-4">
                            <x-input-label for="state" :value="__('Stav')" />
                            <select id="state" name="state" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">
                                <option value="1" selected>Aktivní</option>
                                <option value="0">Neaktivní</option>
                            </select>
                            <x-input-error :messages="$errors->get('state')" class="mt-2" />
                        </div>

                        <!-- Typ -->
                        <div class="mt-4">
                            <x-input-label for="kind" :value="__('Typ')" />
                            <select id="kind" name="kind" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">
                                <option value="1" selected>Standardní</option>
                                <option value="2">Systémová</option>
                                <option value="3">Prioritní</option>
                            </select>
                            <x-input-error :messages="$errors->get('kind')" class="mt-2" />
                        </div>

                        <!-- Přesměrování zpět k projektu -->
                        @if($selectedProject)
                            <input type="hidden" name="redirect_to_project" value="1">
                        @endif

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ $selectedProject ? route('projects.show', $selectedProject) : route('project_items.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-3">
                                {{ __('Zrušit') }}
                            </a>
                            <x-primary-button class="ml-4">
                                {{ __('Vytvořit položku') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
