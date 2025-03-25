<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Media;
use Illuminate\View\View;
use App\Models\ProjectItem;
use Illuminate\Support\Str;
use App\Models\CustomerUser;
use Illuminate\Http\Request;
use App\Models\RequestMessage;
use App\Http\Middleware\IsAdmin;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Models\Request as TicketRequest;

class RequestController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }


    public function index(): View
    {
        $state = request('state');
        $kind = request('kind');

        $requestsQuery = TicketRequest::with(['projectItem.project.customer', 'customerUser']);

        if (Auth::user()->kind != 3) {
            $projectItems = Auth::user()->projectItems()->pluck('id');
            $requestsQuery->whereIn('id_projectitem', $projectItems);
        }

        if (!empty($state)) {
            $requestsQuery->where('state', $state);
        }

        if (!empty($kind)) {
            $requestsQuery->where('kind', $kind);
        }

        $requests = $requestsQuery->paginate(10);

        return view('requests.index', compact('requests'));
    }

    public function projectItemRequests(ProjectItem $projectItem): View
    {
        $requests = TicketRequest::where('id_projectitem', $projectItem->id)
            ->with(['customerUser'])
            ->paginate(10);

        return view('requests.project_item_requests', compact('requests', 'projectItem'));
    }

    public function create(Request $request): View
    {
        $projectItems = [];
        $selectedProjectItem = null;

        if (Auth::user()->kind == 3) {
            $projectItems = ProjectItem::where('state', 1)
                ->with('project.customer')
                ->get();
        } else {
            $projectItems = Auth::user()->projectItems()
                ->where('project_items.state', 1)
                ->with('project.customer')
                ->get();
        }

        if ($request->has('id_projectitem')) {
            $selectedProjectItem = ProjectItem::with('project.customer')->findOrFail($request->id_projectitem);
        }

        $customerUsers = [];
        if (Auth::user()->kind == 3) {
            if ($selectedProjectItem) {
                $customerUsers = $selectedProjectItem->customerUsers;
            } else {
                $customerUsers = CustomerUser::where('state', 1)->get();
            }
        }

        return view('requests.create', compact('projectItems', 'selectedProjectItem', 'customerUsers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id_projectitem' => 'required|exists:project_items,id',
            'id_custuser' => 'required|exists:customer_users,id',
            'name' => 'required|string|max:100',
            'description' => 'required|string|max:1000',
            'state' => 'required|integer',
            'kind' => 'required|integer',
        ]);

        $validated['inserted'] = now();

        $ticketRequest = TicketRequest::create($validated);

        RequestMessage::create([
            'id_request' => $ticketRequest->id,
            'id_custuser' => $validated['id_custuser'],
            'inserted' => now(),
            'state' => 1,
            'kind' => 1,
            'message' => $validated['description'],
        ]);

        if ($request->has('redirect_to_projectitem') && $request->redirect_to_projectitem) {
            return redirect()->route('project_items.show', $validated['id_projectitem'])
                ->with('success', 'Požadavek byl úspěšně vytvořen.');
        }

        return redirect()->route('requests.show', $ticketRequest)
            ->with('success', 'Požadavek byl úspěšně vytvořen.');
    }

    public function show(TicketRequest $request): View
    {
        $request->load([
            'projectItem.project.customer',
            'customerUser',
            'messages' => function ($query) {
                $query->orderBy('inserted', 'asc');
            },
            'messages.user',
            'messages.customerUser',
            'reports' => function ($query) {
                $query->orderBy('inserted', 'desc');
            },
            'reports.user'
        ]);

        if ($request->messages->isNotEmpty()) {
            foreach ($request->messages as $message) {
                if ($message->id_user && !$message->relationLoaded('user')) {
                    $message->load('user');
                }

                if ($message->id_custuser && !$message->relationLoaded('customerUser')) {
                    $message->load('customerUser');
                }
            }
        }

        return view('requests.show', compact('request'));
    }

    public function edit(TicketRequest $request): View
    {
        $request->load(['projectItem.project.customer', 'customerUser']);

        return view('requests.edit', compact('request'));
    }

    public function update(Request $httpRequest, TicketRequest $request): RedirectResponse
    {
        $validated = $httpRequest->validate([
            'name' => 'required|string|max:100',
            'state' => 'required|integer',
            'kind' => 'required|integer',
        ]);

        $request->update($validated);

        return redirect()->route('requests.show', $request)
            ->with('success', 'Požadavek byl úspěšně aktualizován.');
    }

    public function changeState(Request $httpRequest, TicketRequest $request): RedirectResponse
    {
        $validated = $httpRequest->validate([
            'state' => 'required|integer',
        ]);

        $request->update(['state' => $validated['state']]);

        return back()->with('success', 'Stav požadavku byl úspěšně změněn.');
    }

    public function addMessage(Request $httpRequest, TicketRequest $request): RedirectResponse
    {
        $validated = $httpRequest->validate([
            'message' => 'required|string|max:1000',
            'file' => 'nullable|file|max:10240', // 10MB max
        ]);

        $messageData = [
            'id_request' => $request->id,
            'inserted' => now(),
            'state' => 1,
            'kind' => 1,
            'message' => $validated['message'],
        ];

        if (Auth::guard('web')->check()) {
            $messageData['id_user'] = Auth::id();
        } else if (Auth::guard('customer')->check()) {
            $messageData['id_custuser'] = Auth::guard('customer')->id();
        }

        if (!isset($messageData['id_user']) && !isset($messageData['id_custuser'])) {
            $messageData['id_user'] = Auth::id() ?? 1;
        }

        $message = RequestMessage::create($messageData);

        // Handle file upload if provided
        if ($httpRequest->hasFile('file')) {
            $file = $httpRequest->file('file');
            $originalName = $file->getClientOriginalName();
            $fileName = Str::uuid() . '_' . $originalName;

            // Store file in storage/app/public/uploads directory
            $filePath = $file->storeAs('uploads', $fileName, 'public');

            if ($filePath) {
                // Create media record
                $media = Media::create([
                    'state' => 1,
                    'kind' => $this->determineMediaKind($file->getMimeType()),
                    'name' => $originalName,
                    'file' => $fileName, // Store just the filename
                ]);

                // Attach media to the message
                $message->media()->attach($media->id);
            }
        }

        return back()->with('success', 'Zpráva byla úspěšně přidána.');
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
}
