<?php

namespace App\Http\Controllers;

use App\Models\Request as RequestModel;
use App\Models\RequestOffers;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RequestOfferController extends Controller
{
    public function create(Request $request)
    {
        $selectedRequest = null;
        $requests = collect(); // Initialize as an empty collection

        if ($request->has('request_id')) {
            $selectedRequest = RequestModel::findOrFail($request->request_id);
        } else {
            // Získat všechny požadavky pro výběr, pokud není vybrán konkrétní
            $requests = RequestModel::with('customerUser')->get();
        }

        return view('request_offers.create', compact('selectedRequest', 'requests'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_request' => 'required|exists:requests,id',
            'price' => 'required|numeric',
            'name' => 'required|string|max:100',
            'file' => 'required|file|max:10240',
        ]);

        $offer = new RequestOffers();
        $offer->id_request = $validated['id_request'];
        $offer->price = $validated['price'];
        $offer->name = $validated['name'];

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('offers', 'public');
            $offer->file_path = $path;
            $offer->file_name = $file->getClientOriginalName();
            $offer->file_mime = $file->getMimeType();
        }

        $offer->save();

        // Zde je důležitá změna - předáváme ID požadavku jako parametr
        $requestId = $validated['id_request'];
        return redirect()->route('requests.show', ['request' => $requestId])
            ->with('success', 'Nabídka byla úspěšně vytvořena.');
    }

    public function edit(RequestOffers $requestOffer)
    {

        $requestOffer->load('request');

        return view('request_offers.edit', compact('requestOffer'));
    }

    public function update(Request $request, RequestOffers $requestOffer)
    {
        $validated = $request->validate([
            'price' => 'required|numeric',
            'name' => 'required|string|max:100',
        ]);

        $data = [
            'price' => $validated['price'],
            'name' => $validated['name'],
        ];

        // Aktualizace souboru pouze pokud byl poskytnut nový
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('offers', 'public');
            $data['file_path'] = $path;
            $data['file_name'] = $file->getClientOriginalName();
            $data['file_mime'] = $file->getMimeType();
        }
        foreach ($data as $key => $value) {
            $requestOffer->$key = $value;
        }

        $requestOffer->save();

        return redirect()->route('requests.show', $requestOffer->id_request)
            ->with('success', 'Nabídka byla úspěšně upravena.');
    }

    public function destroy(RequestOffers $requestOffer)
    {
        $requestId = $requestOffer->id_request;
        $requestOffer->delete();

        return redirect()->route('requests.show', $requestId)
            ->with('success', 'Nabídka byla úspěšně smazána.');
    }

    public function preview(RequestOffers $requestOffer)
    {
        // Určení MIME typu
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->buffer($requestOffer->file);

        // Vrátí soubor pro náhled
        return response($requestOffer->file)
            ->header('Content-Type', $mimeType);
    }

    public function download(RequestOffers $requestOffer)
    {
        // Určení MIME typu
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->buffer($requestOffer->file);

        // Generování názvu souboru
        $extension = explode('/', $mimeType)[1] ?? 'bin';
        $filename = Str::slug($requestOffer->name) . '.' . $extension;

        // Vrátí soubor ke stažení
        return response($requestOffer->file)
            ->header('Content-Type', $mimeType)
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }
}
