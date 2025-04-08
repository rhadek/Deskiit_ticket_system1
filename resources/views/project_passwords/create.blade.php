<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Vytvořit nové projektové heslo') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('project_passwords.store') }}">
                        @csrf

                        <div>
                            <x-input-label for="id_project" :value="__('Projekt')" />
                            @if($selectedProject)
                                <x-text-input id="id_project" class="block mt-1 w-full bg-gray-100" type="text" name="id_project" :value="$selectedProject->name . ' (' . $selectedProject->customer->name . ')'" readonly />
                                <input type="hidden" name="id_project" value="{{ $selectedProject->id }}">
                            @else
                                <select id="id_project" name="id_project" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">
                                    @foreach($projects as $project)
                                        <option value="{{ $project->id }}" {{ $selectedProject && $selectedProject->id == $project->id ? 'selected' : '' }}>
                                            {{ $project->name }} ({{ $project->customer->name }})
                                        </option>
                                    @endforeach
                                </select>
                            @endif
                            <x-input-error :messages="$errors->get('id_project')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="name" :value="__('Název hesla')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="login" :value="__('Login')" />
                            <x-text-input id="login" class="block mt-1 w-full" type="text" name="login" :value="old('login')" required autofocus />
                            <x-input-error :messages="$errors->get('login')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="password" :value="__('Heslo')" />
                            <x-text-input id="password" class="block mt-1 w-full" type="text" name="password" :value="old('password')" required autofocus />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        @if($selectedProject)
                            <input type="hidden" name="redirect_to_project" value="1">
                        @endif

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ $selectedProject ? route('projects.show', $selectedProject) : route('projects.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-3">
                                {{ __('Zrušit') }}
                            </a>
                            <x-primary-button class="ml-4">
                                {{ __('Vytvořit heslo') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
</x-app-layout>
