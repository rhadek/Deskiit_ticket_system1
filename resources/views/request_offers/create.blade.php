<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Vytvořit nabídku') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('offers.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        @if($selectedRequest)
                            <input type="hidden" name="id_request" value="{{ $selectedRequest->id }}">
                            <div class="mb-6">
                                <div class="bg-indigo-50 dark:bg-indigo-900/30 p-4 rounded-lg">
                                    <h3 class="font-medium text-indigo-800 dark:text-indigo-300 mb-2">Vybraný požadavek</h3>
                                    <p class="text-indigo-700 dark:text-indigo-400">{{ $selectedRequest->name }}</p>
                                </div>
                            </div>
                        @else
                            <div class="mb-6">
                                <label for="id_request" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Požadavek</label>
                                <select name="id_request" id="id_request" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                    <option value="">Vyberte požadavek</option>
                                    @foreach($requests as $request)
                                        <option value="{{ $request->id }}">{{ $request->name }} ({{ $request->customerUser->fname }} {{ $request->customerUser->lname }})</option>
                                    @endforeach
                                </select>
                                @error('id_request')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif

                        <div class="mb-6">
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Název nabídky</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                            @error('name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="price" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Cena (Kč)</label>
                            <input type="number" step="0.01" name="price" id="price" value="{{ old('price') }}" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                            @error('price')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="file" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Příloha</label>
                            <input type="file" name="file" id="file" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-800 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Podporované formáty: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG</p>
                            @error('file')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end">
                            <a href="{{ $selectedRequest ? route('requests.show', $selectedRequest) : route('requests.index') }}" class="bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md py-2 px-4 hover:bg-gray-300 dark:hover:bg-gray-600 mr-2">
                                Zrušit
                            </a>
                            <button type="submit" class="bg-indigo-600 text-white rounded-md py-2 px-4 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Vytvořit nabídku
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
