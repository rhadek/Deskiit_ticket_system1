{{-- resources/views/components/media-display.blade.php --}}

@props(['media', 'entityType', 'entityId', 'showDeleteButton' => true])

<div class="border rounded-lg p-4 mb-4">
    <div class="mb-2 flex justify-between items-center">
        <h4 class="text-md font-medium text-gray-900">Přiložené soubory</h4>
        @if(count($media) > 0 && $showDeleteButton)
            <button type="button" class="toggle-media-upload text-blue-600 hover:text-blue-800 text-sm flex items-center" onclick="toggleMediaUploadForm()">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Nahrát soubor
            </button>
        @endif
    </div>

    <div id="media-upload-form" class="mb-4 {{ count($media) > 0 ? 'hidden' : '' }}">
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

            <div class="flex justify-end">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                    Nahrát soubor
                </button>
            </div>
        </form>
    </div>

    @if(count($media) > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($media as $file)
                <div class="border rounded-lg overflow-hidden bg-white shadow-sm hover:shadow-md transition">
                    <div class="p-3 border-b bg-gray-50">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium truncate" title="{{ $file->name }}">
                                {{ Str::limit($file->name, 20) }}
                            </span>
                            @if($showDeleteButton)
                                <form action="{{ route('media.destroy', $file->id) }}" method="POST" class="inline" onsubmit="return confirm('Opravdu chcete smazat tento soubor?');">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="redirect_url" value="{{ url()->current() }}">
                                    <button type="submit" class="text-red-600 hover:text-red-800">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    <div class="p-4 flex flex-col items-center">
                        <div class="mb-3 h-16 flex items-center justify-center">
                            @if($file->kind == 1)
                                {{-- Image --}}
                                <img src="{{ route('media.show', $file->id) }}" alt="{{ $file->name }}" class="max-h-16 max-w-full object-contain">
                            @elseif($file->kind == 2)
                                {{-- PDF --}}
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                            @elseif($file->kind == 3)
                                {{-- Office Document --}}
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            @elseif($file->kind == 4)
                                {{-- Text file --}}
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            @elseif($file->kind == 5)
                                {{-- Archive --}}
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                </svg>
                            @else
                                {{-- Other --}}
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                            @endif
                        </div>

                        <div class="w-full">
                            <a href="{{ route('media.download', $file->id) }}" class="inline-flex justify-center w-full items-center px-3 py-1.5 bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-md text-xs font-medium text-gray-700 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                Stáhnout
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center p-6 text-gray-500">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <p>Žádné soubory nebyly nahrány</p>
            <button type="button" class="mt-2 text-blue-600 hover:text-blue-800 text-sm flex items-center mx-auto" onclick="toggleMediaUploadForm()">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Nahrát soubor
            </button>
        </div>
    @endif
</div>

<script>
    function toggleMediaUploadForm() {
        const form = document.getElementById('media-upload-form');
        form.classList.toggle('hidden');
    }
</script>
