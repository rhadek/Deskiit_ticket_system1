{{-- resources/views/components/media-upload.blade.php --}}

@props(['entityType', 'entityId'])

<div class="mt-4">
    <button type="button" id="show-upload-form-button" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
        </svg>
        {{ __('Nahrát přílohu') }}
    </button>

    <div id="media-upload-form-container" class="mt-4 hidden">
        <form action="{{ route('media.store') }}" method="POST" enctype="multipart/form-data" class="border-2 border-dashed border-gray-300 rounded-lg p-4">
            @csrf
            <input type="hidden" name="entity_type" value="{{ $entityType }}">
            <input type="hidden" name="entity_id" value="{{ $entityId }}">
            <input type="hidden" name="redirect_url" value="{{ url()->current() }}">

            <div class="mb-4">
                <label for="file" class="block text-sm font-medium text-gray-700 mb-2">Vyberte soubor k nahrání</label>
                <input type="file" id="file" name="file" class="block w-full text-sm text-gray-500
                    file:mr-4 file:py-2 file:px-4
                    file:rounded-md file:border-0
                    file:text-sm file:font-semibold
                    file:bg-indigo-50 file:text-indigo-700
                    hover:file:bg-indigo-100" required>
                <p class="mt-1 text-xs text-gray-500">Maximální velikost souboru: 10MB</p>
                <p class="text-xs text-gray-500">Podporované typy souborů: obrázky, PDF, dokumenty Office, textové soubory, CSV, ZIP</p>
            </div>

            <div class="flex justify-end space-x-3">
                <button type="button" id="cancel-upload-button" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300">
                    Zrušit
                </button>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                    Nahrát soubor
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const showButton = document.getElementById('show-upload-form-button');
        const cancelButton = document.getElementById('cancel-upload-button');
        const formContainer = document.getElementById('media-upload-form-container');

        if (showButton && formContainer) {
            showButton.addEventListener('click', function() {
                formContainer.classList.remove('hidden');
                showButton.classList.add('hidden');
            });
        }

        if (cancelButton && formContainer && showButton) {
            cancelButton.addEventListener('click', function() {
                formContainer.classList.add('hidden');
                showButton.classList.remove('hidden');
            });
        }
    });
</script>
