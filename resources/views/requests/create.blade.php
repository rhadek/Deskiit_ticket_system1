<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Vytvořit nový požadavek') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('requests.store') }}">
                        @csrf


                        <div>
                            <x-input-label for="id_projectitem" :value="__('Projektová položka')" />
                            <select id="id_projectitem" name="id_projectitem" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">
                                @foreach($projectItems as $item)
                                    <option value="{{ $item->id }}" {{ $selectedProjectItem && $selectedProjectItem->id == $item->id ? 'selected' : '' }}>
                                        {{ $item->name }} ({{ $item->project->name }} - {{ $item->project->customer->name }})
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('id_projectitem')" class="mt-2" />
                        </div>


                        @if(auth()->user()->kind == 3)
                        <div class="mt-4">
                            <x-input-label for="id_custuser" :value="__('Zákaznický uživatel')" />
                            <select id="id_custuser" name="id_custuser" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">
                                @foreach($customerUsers as $user)
                                    <option value="{{ $user->id }}">
                                        {{ $user->fname }} {{ $user->lname }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('id_custuser')" class="mt-2" />
                        </div>
                        @endif


                        <div class="mt-4">
                            <x-input-label for="name" :value="__('Název požadavku')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>


                        <div class="mt-4">
                            <x-input-label for="description" :value="__('Popis požadavku')" />
                            <textarea id="description" name="description" rows="5" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" required>{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>


                        <div class="mt-4">
                            <x-input-label for="state" :value="__('Stav')" />
                            <select id="state" name="state" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">
                                <option value="1" selected>Nový</option>
                                <option value="2">V řešení</option>
                                <option value="3">Čeká na zpětnou vazbu</option>
                                <option value="4">Vyřešeno</option>
                                <option value="5">Uzavřeno</option>
                            </select>
                            <x-input-error :messages="$errors->get('state')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="kind" :value="__('Typ')" />
                            <select id="kind" name="kind" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">
                                <option value="1" selected>Standardní</option>
                                <option value="2">Chyba</option>
                                <option value="3">Prioritní</option>
                            </select>
                            <x-input-error :messages="$errors->get('kind')" class="mt-2" />
                        </div>


                        @if($selectedProjectItem)
                            <input type="hidden" name="redirect_to_projectitem" value="1">
                        @endif

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ $selectedProjectItem ? route('project_items.show', $selectedProjectItem) : route('requests.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-3">
                                {{ __('Zrušit') }}
                            </a>
                            <x-primary-button class="ml-4">
                                {{ __('Vytvořit požadavek') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
