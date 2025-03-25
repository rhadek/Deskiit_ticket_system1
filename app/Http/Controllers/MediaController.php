<?php

namespace App\Http\Controllers;

use App\Models\Media;
use App\Models\Request as TicketRequest;
use App\Models\RequestMessage;
use App\Models\RequestReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller as BaseController;
class MediaController extends BaseController
{
    protected $allowedMimeTypes = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // .docx
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // .xlsx
        'text/plain',
        'text/csv',
        'application/zip'
    ];

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Store a newly uploaded media file.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
            'entity_type' => 'required|string|in:request,message,report',
            'entity_id' => 'required|integer',
            'redirect_url' => 'required|string'
        ]);

        // Validate that the file mime type is allowed
        if (!in_array($request->file('file')->getMimeType(), $this->allowedMimeTypes)) {
            return back()->with('error', 'Nepodporovaný typ souboru.');
        }

        // Check if the entity exists based on entity_type
        $entity = null;
        switch ($request->entity_type) {
            case 'request':
                $entity = TicketRequest::findOrFail($request->entity_id);
                break;
            case 'message':
                $entity = RequestMessage::findOrFail($request->entity_id);
                break;
            case 'report':
                $entity = RequestReport::findOrFail($request->entity_id);
                break;
        }

        // Store the file
        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $fileName = Str::uuid() . '_' . $originalName;

        // Store file in storage/app/public/uploads directory
        $filePath = $file->storeAs('uploads', $fileName, 'public');

        if (!$filePath) {
            return back()->with('error', 'Chyba při nahrávání souboru.');
        }

        // Create media record
        $media = Media::create([
            'state' => 1,
            'kind' => $this->determineKind($file->getMimeType()),
            'name' => $originalName,
            'file' => $fileName, // Store just the filename
        ]);

        // Attach media to the entity
        switch ($request->entity_type) {
            case 'request':
                $entity->media()->attach($media->id);
                break;
            case 'message':
                $entity->media()->attach($media->id);
                break;
            case 'report':
                $entity->media()->attach($media->id);
                break;
        }

        return redirect($request->redirect_url)
            ->with('success', 'Soubor byl úspěšně nahrán.');
    }

    /**
     * Download a media file.
     */
    public function download(Media $media)
    {
        // Check if file exists
        if (!Storage::disk('public')->exists('uploads/' . $media->file)) {
            return back()->with('error', 'Soubor nenalezen.');
        }

        return Storage::disk('public')->download('uploads/' . $media->file, $media->name);
    }

    /**
     * Display a media file.
     */
    public function show(Media $media)
    {
        // Check if file exists
        if (!Storage::disk('public')->exists('uploads/' . $media->file)) {
            return back()->with('error', 'Soubor nenalezen.');
        }

        // For images, PDFs and other previewable types, display them
        $mimeType = Storage::disk('public')->mimeType('uploads/' . $media->file);

        if (Str::startsWith($mimeType, 'image/')) {
            return response()->file(Storage::disk('public')->path('uploads/' . $media->file));
        }

        // For other files, download them
        return Storage::disk('public')->download('uploads/' . $media->file, $media->name);
    }

    /**
     * Delete a media file.
     */
    public function destroy(Media $media, Request $request): RedirectResponse
    {
        // Check permissions - implement as needed based on your user roles

        // Delete file from storage
        if (Storage::disk('public')->exists('uploads/' . $media->file)) {
            Storage::disk('public')->delete('uploads/' . $media->file);
        }

        // Detach from all relationships and delete record
        $media->requests()->detach();
        $media->requestMessages()->detach();
        $media->requestReports()->detach();
        $media->delete();

        return redirect($request->redirect_url ?? url()->previous())
            ->with('success', 'Soubor byl úspěšně smazán.');
    }

    /**
     * Determine the kind of media based on mime type.
     */
    private function determineKind(string $mimeType): int
    {
        if (Str::startsWith($mimeType, 'image/')) {
            return 1; // Image
        } elseif ($mimeType === 'application/pdf') {
            return 2; // PDF
        } elseif (Str::contains($mimeType, 'word') || Str::contains($mimeType, 'excel') || Str::contains($mimeType, 'spreadsheet')) {
            return 3; // Office Document
        } elseif ($mimeType === 'text/plain' || $mimeType === 'text/csv') {
            return 4; // Text file
        } elseif ($mimeType === 'application/zip') {
            return 5; // Archive
        } else {
            return 99; // Other
        }
    }
}
