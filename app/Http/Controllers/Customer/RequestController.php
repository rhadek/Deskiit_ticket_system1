<?php

namespace App\Http\Controllers\Customer;

use Illuminate\Routing\Controller;
use App\Models\Request as TicketRequest;
use App\Models\RequestMessage;
use App\Models\ProjectItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:customer');
    }

    public function index(Request $request)
    {
        $user = Auth::guard('customer')->user();

        if ($user->kind == 3) {
            $query = TicketRequest::whereHas('projectItem.project', function ($q) use ($user) {
                $q->where('id_customer', $user->id_customer);
            })->with(['projectItem.project']);
        } else {
            $query = TicketRequest::where('id_custuser', $user->id)
                ->with(['projectItem.project']);
        }

        if ($request->has('state') && $request->state != '') {
            $query->where('state', $request->state);
        }

        if ($request->has('kind') && $request->kind != '') {
            $query->where('kind', $request->kind);
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('customer.requests.index', compact('requests'));
    }

    public function show(TicketRequest $request)
    {
        $user = Auth::guard('customer')->user();

        $request->load('projectItem.project');

        if ($request->projectItem->project->id_customer !== $user->id_customer) {
            abort(403, 'Neautorizovaný přístup.');
        }

        if ($user->kind != 3 && $request->id_custuser !== $user->id) {
            abort(403, 'Neautorizovaný přístup.');
        }

        $request->load([
            'projectItem.project.customer',
            'messages' => function ($query) {
                $query->orderBy('inserted', 'asc');
            },
            'messages.user',
            'messages.customerUser',
        ]);

        return view('customer.requests.show', compact('request'));
    }

    public function create(Request $request, $id_projectitem = null)
    {
        $user = Auth::guard('customer')->user();

        $query = $user->projectItems()
            ->where('project_items.state', 1)
            ->with('project');

        $selectedProjectItem = null;
        if ($id_projectitem) {
            $selectedProjectItem = ProjectItem::find($id_projectitem);

            if (!$selectedProjectItem || !$user->projectItems()->where('project_items.id', $id_projectitem)->exists()) {
                return redirect()->route('customer.requests.create')
                    ->with('error', 'Nemáte přístup k vybrané projektové položce.');
            }
        }

        $projectItems = $query->get();

        return view('customer.requests.create', compact('projectItems', 'selectedProjectItem'));
    }

    public function store(Request $request)
    {
        $user = Auth::guard('customer')->user();

        $validated = $request->validate([
            'id_projectitem' => [
                'required',
                'exists:project_items,id',
                function ($attribute, $value, $fail) use ($user) {
                    $hasAccess = $user->projectItems()->where('project_items.id', $value)->exists();
                    if (!$hasAccess) {
                        $fail('Nemáte přístup k této projektové položce.');
                    }
                }
            ],
            'name' => 'required|string|max:100',
            'description' => 'required|string|max:1000',
            'kind' => 'required|integer|in:1,2,3',
        ]);

        $validated['id_custuser'] = $user->id;
        $validated['inserted'] = now();
        $validated['state'] = 1;

        $ticketRequest = TicketRequest::create($validated);

        RequestMessage::create([
            'id_request' => $ticketRequest->id,
            'id_custuser' => $user->id,
            'inserted' => now(),
            'state' => 1,
            'kind' => 1,
            'message' => $validated['description'],
        ]);

        return redirect()->route('customer.requests.show', $ticketRequest)
            ->with('success', 'Požadavek byl úspěšně vytvořen.');
    }


    public function addMessage(Request $request, $id)
    {
        $user = Auth::guard('customer')->user();

        $ticketRequest = \App\Models\Request::findOrFail($id);

        if ($ticketRequest->id_custuser !== $user->id && $user->kind != 3) {
            abort(403, 'Neautorizovaný přístup.');
        }

        if ($ticketRequest->state == 5) {
            return back()->with('error', 'Nelze přidat zprávu k uzavřenému požadavku.');
        }

        $validated = $request->validate([
            'message' => 'required|string|max:1000',
            'file' => 'nullable|file|max:10240', // 10MB max
        ]);

        $message = RequestMessage::create([
            'id_request' => $ticketRequest->id,
            'id_custuser' => $user->id,
            'id_user' => null,
            'inserted' => now(),
            'state' => 1,
            'kind' => 1,
            'message' => $validated['message'],
        ]);

        // Handle file upload if provided
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $originalName = $file->getClientOriginalName();
            $fileName = \Illuminate\Support\Str::uuid() . '_' . $originalName;

            // Store file in storage/app/public/uploads directory
            $filePath = $file->storeAs('uploads', $fileName, 'public');

            if ($filePath) {
                // Create media record
                $media = \App\Models\Media::create([
                    'state' => 1,
                    'kind' => $this->determineMediaKind($file->getMimeType()),
                    'name' => $originalName,
                    'file' => $fileName, // Store just the filename
                ]);

                // Attach media to the message
                $message->media()->attach($media->id);
            }
        }

        if ($ticketRequest->state == 4 || $ticketRequest->state == 3) {
            $ticketRequest->state = 2;
            $ticketRequest->save();
        }

        return redirect()->route('customer.requests.show', $ticketRequest->id)
            ->with('success', 'Zpráva byla úspěšně přidána.');
    }

    /**
     * Helper method to determine the kind of media based on mime type.
     */
    private function determineMediaKind(string $mimeType): int
    {
        if (str_starts_with($mimeType, 'image/')) {
            return 1; // Image
        } elseif ($mimeType === 'application/pdf') {
            return 2; // PDF
        } elseif (str_contains($mimeType, 'word') || str_contains($mimeType, 'excel') || str_contains($mimeType, 'spreadsheet')) {
            return 3; // Office Document
        } elseif ($mimeType === 'text/plain' || $mimeType === 'text/csv') {
            return 4; // Text file
        } elseif ($mimeType === 'application/zip') {
            return 5; // Archive
        } else {
            return 99; // Other
        }
    }


    public function confirmResolution($id)
    {
        $user = Auth::guard('customer')->user();
        $ticketRequest = \App\Models\Request::findOrFail($id);


        if ($ticketRequest->id_custuser !== $user->id && $user->kind != 3) {
            abort(403, 'Neautorizovaný přístup.');
        }


        if ($ticketRequest->state != 4) {
            return back()->with('error', 'Požadavek musí být ve stavu "Vyřešeno", aby mohl být uzavřen.');
        }


        $ticketRequest->state = 5;
        $ticketRequest->save();

        RequestMessage::create([
            'id_request' => $ticketRequest->id,
            'id_custuser' => $user->id,
            'inserted' => now(),
            'state' => 1,
            'kind' => 1,
            'message' => 'Požadavek byl potvrzen jako vyřešený a uzavřen.',
        ]);

        return redirect()->route('customer.requests.show', $ticketRequest)
            ->with('success', 'Požadavek byl úspěšně uzavřen.');
    }
}
