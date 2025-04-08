<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Upravit prioritu projektu: ') }} {{ $projectPriority->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('project_priorities.update', $projectPriority) }}">
                        @csrf
                        @method('PUT')

                        <input type="hidden" name="id_project" value="{{ $projectPriority->id_project }}">

                        <div class="mt-4">
                            <x-input-label for="name" :value="__('Název priority')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $projectPriority->name)" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="kind" :value="__('Úroveň priority')" />
                            <x-text-input id="kind" class="block mt-1 w-full" type="number" name="kind" :value="old('kind', $projectPriority->kind)" required />
                            <x-input-error :messages="$errors->get('kind')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="execution_time_limit" :value="__('Rychlost reakce od zadání (v hodinách)')" />
                            <x-text-input id="execution_time_limit" class="block mt-1 w-full" type="number" name="execution_time_limit" :value="old('execution_time_limit', $projectPriority->execution_time_limit)" step="0.1" required />
                            <x-input-error :messages="$errors->get('execution_time_limit')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('projects.show', $projectPriority->id_project) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-3">
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
