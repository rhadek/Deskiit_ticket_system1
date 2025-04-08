<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Upravit nabídku') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('offers.update', $requestOffer) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-6">
                            <div class="bg-indigo-50 dark:bg-indigo-900/30 p-4 rounded-lg">
                                <h3 class="font-medium text-indigo-800 dark:text-indigo-300 mb-2">Požadavek</h3>
                                <p class="text-indigo-700 dark:text-indigo-400">{{ $requestOffer->request->name }}</p>
                            </div>
                        </div>

                        <div class="mb-6">
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Název nabídky</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $requestOffer->name) }}" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                            @error('name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="price" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Cena (Kč)</label>
                            <input type="number" step="0.01" name="price" id="price" value="{{ old('price', $requestOffer->price) }}" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                            @error('price')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="file" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Příloha</label>
                            @if($requestOffer->file_path)
                                <div class="mb-2 flex items-center">
                                    <span class="text-sm text-gray-600 dark:text-gray-400 mr-2">Aktuální soubor:</span>
                                    <a href="{{ asset('storage/' . $requestOffer->file_path) }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline" target="_blank">
                                        {{ basename($requestOffer->file_path) }}
                                    </a>
                                </div>
                            @endif
                            <input type="file" name="file" id="file" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-800 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Nechte prázdné, pokud nechcete měnit přílohu. Podporované formáty: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG</p>
                            @error('file')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end">
                            <a href="{{ route('requests.show', $requestOffer->id_request) }}" class="bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md py-2 px-4 hover:bg-gray-300 dark:hover:bg-gray-600 mr-2">
                                Zrušit
                            </a>
                            <button type="submit" class="bg-indigo-600 text-white rounded-md py-2 px-4 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Uložit změny
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
