<?php

namespace App\Http\Controllers\Customer;

use App\Models\Media;
use App\Models\ProjectItem;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\RequestMessage;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Request as TicketRequest;

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

    // Diagnostické výpisy
    $projectItems = ProjectItem::all(); // Načtení VŠECH položek bez filtrace
    $user_items = $user->projectItems; // Načtení položek přes relaci

    // Výpis do logu
    \Log::info('All project items: ' . $projectItems->count());
    \Log::info('Customer user project items: ' . $user_items->count());

    // Přímé načtení přes SQL pro srovnání
    $user_id = $user->id;
    $sql_items = \DB::table('project_items')
        ->join('custuser_x_projectitem', 'project_items.id', '=', 'custuser_x_projectitem.id_projectitem')
        ->where('custuser_x_projectitem.id_custuser', $user_id)
        ->get();

    \Log::info('SQL project items: ' . count($sql_items));

    // Standardní logika
    $selectedProjectItem = null;

    // Pokud přicházíme s id_projectitem jako argumentem nebo v URL
    if ($id_projectitem) {
        $selectedProjectItem = ProjectItem::with('project.customer')
            ->find($id_projectitem);
    } elseif ($request->has('id_projectitem')) {
        $selectedProjectItem = ProjectItem::with('project.customer')
            ->find($request->id_projectitem);
    }

    // Použití úplně jiného přístupu k načtení položek:
    if ($user->kind == 3) {
        // Admin vidí všechny položky projektu své firmy
        $projectItems = ProjectItem::whereHas('project', function($query) use ($user) {
            $query->where('id_customer', $user->id_customer);
        })->with('project')->get();
    } else {
        // Pokus o přístup přes tabulku a JOIN, ne přes relaci
        $projectItems = ProjectItem::join('custuser_x_projectitem', 'project_items.id', '=', 'custuser_x_projectitem.id_projectitem')
            ->where('custuser_x_projectitem.id_custuser', $user->id)
            ->select('project_items.*')
            ->with('project')
            ->get();
    }

    // Další diagnostika


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

    public function addMessage(Request $request, $id)
    {
        $user = Auth::guard('customer')->user();

        $ticketRequest = TicketRequest::findOrFail($id);

        // Ensure the user has access to this request
        if ($ticketRequest->id_custuser !== $user->id && $user->kind != 3) {
            abort(403, 'Neautorizovaný přístup.');
        }

        // Check if request is closed
        if ($ticketRequest->state == 5) {
            return back()->with('error', 'Nelze přidat zprávu k uzavřenému požadavku.');
        }

        // Validate message
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
            'file' => 'nullable|file|max:10240', // 10MB max
        ]);

        // Create message
        $message = RequestMessage::create([
            'id_request' => $ticketRequest->id,
            'id_custuser' => $user->id,
            'inserted' => now(),
            'state' => 1,
            'kind' => 1,
            'message' => $validated['message'],
        ]);

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');

            // Validate file type
            if (!in_array($file->getMimeType(), $this->allowedMimeTypes)) {
                return back()->with('error', 'Nepodporovaný typ souboru.');
            }

            // Generate unique filename
            $originalName = $file->getClientOriginalName();
            $fileName = Str::uuid() . '_' . $originalName;

            // Store file
            $filePath = $file->storeAs('uploads', $fileName, 'public');

            if ($filePath) {
                // Create media record
                $media = Media::create([
                    'state' => 1,
                    'kind' => $this->determineMediaKind($file->getMimeType()),
                    'name' => $originalName,
                    'file' => $fileName,
                ]);

                // Attach media to the message
                $message->media()->attach($media->id);
            }
        }

        // Update request state if necessary
        if ($ticketRequest->state == 4 || $ticketRequest->state == 3) {
            $ticketRequest->state = 2;
            $ticketRequest->save();
        }

        return redirect()->route('customer.requests.show', $ticketRequest->id)
            ->with('success', 'Zpráva byla úspěšně přidána.');
    }

    /**
     * Determine the kind of media based on mime type.
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
}
