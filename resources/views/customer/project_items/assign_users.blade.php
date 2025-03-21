<x-customer-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Přiřazení uživatelů k položce: ') }} {{ $projectItem->name }}
        </h2>
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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Informace o položce</h3>

                    <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-semibold">Název:</p>
                            <p class="text-gray-700">{{ $projectItem->name }}</p>
                        </div>

                        <div>
                            <p class="text-sm font-semibold">Projekt:</p>
                            <p class="text-gray-700">{{ $projectItem->project->name }}</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('customer.project_items.store_assignments', $projectItem) }}">
                        @csrf

                        <h3 class="text-lg font-medium mb-4">Vyberte uživatele, kteří budou mít přístup k této položce</h3>

                        <div class="mt-4">
                            <div class="bg-gray-50 p-4 rounded-lg border">
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @foreach($customerUsers as $user)
                                        <div class="flex items-start">
                                            <div class="flex items-center h-5">
                                                <input
                                                    id="user-{{ $user->id }}"
                                                    name="user_ids[]"
                                                    type="checkbox"
                                                    value="{{ $user->id }}"
                                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                                    {{ $projectItem->customerUsers->contains($user->id) ? 'checked' : '' }}
                                                >
                                            </div>
                                            <div class="ml-3 text-sm">
                                                <label for="user-{{ $user->id }}" class="font-medium text-gray-700">
                                                    {{ $user->fname }} {{ $user->lname }}
                                                </label>
                                                <p class="text-gray-500">{{ $user->email }}</p>
                                                <p class="text-xs text-gray-400">
                                                    @if ($user->kind == 1)
                                                        Běžný uživatel
                                                    @elseif ($user->kind == 2)
                                                        Pokročilý uživatel
                                                    @elseif ($user->kind == 3)
                                                        Admin
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('user_ids')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('customer.project_items.show', $projectItem) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-3">
                                {{ __('Zrušit') }}
                            </a>
                            <x-primary-button class="ml-4">
                                {{ __('Uložit přiřazení') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-customer-layout>
