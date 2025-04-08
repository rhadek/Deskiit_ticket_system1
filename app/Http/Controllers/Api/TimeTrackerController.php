<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Request as TicketRequest;
use App\Models\TimeTrackerSession;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TimeTrackerController extends Controller
{
    /**
     * Get the name and details of a request by ID.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function getRequestName($id): JsonResponse
    {
        $request = TicketRequest::with(['projectItem.project', 'customerUser'])
            ->find($id);

        if (!$request) {
            return response()->json([
                'name' => null,
                'message' => 'Request not found'
            ], 404);
        }

        return response()->json([
            'id' => $request->id,
            'name' => $request->name,
            'projectItem' => [
                'id' => $request->projectItem->id,
                'name' => $request->projectItem->name
            ],
            'project' => [
                'id' => $request->projectItem->project->id,
                'name' => $request->projectItem->project->name
            ],
            'customer' => [
                'id' => $request->projectItem->project->customer->id,
                'name' => $request->projectItem->project->customer->name
            ]
        ]);
    }

    /**
     * Start a time tracking session.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function startTracking(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id_request' => 'required|exists:requests,id',
            'start_time' => 'required|date'
        ]);

        // Zkontrolovat, zda již nemáme aktivní session
        $activeSession = TimeTrackerSession::where('id_user', Auth::id())
            ->where('completed', false)
            ->whereNull('end_time')
            ->first();

        if ($activeSession) {
            return response()->json([
                'success' => false,
                'message' => 'Již máte aktivní časovač',
                'active_session' => $activeSession
            ], 400);
        }

        // Vytvořit novou časovou session
        $session = TimeTrackerSession::create([
            'id_request' => $validated['id_request'],
            'id_user' => Auth::id(),
            'start_time' => Carbon::parse($validated['start_time']),
            'completed' => false
        ]);

        // Aktualizovat stav požadavku na "V řešení" (state = 2), pokud byl "Nový" (state = 1)
        $ticketRequest = TicketRequest::find($validated['id_request']);
        if ($ticketRequest->state == 1) {
            $ticketRequest->update(['state' => 2]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Časovač byl spuštěn',
            'session' => $session,
            'request_updated' => $ticketRequest->state == 2
        ]);
    }

    /**
     * Stop a time tracking session.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function stopTracking(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id_request' => 'required|exists:requests,id',
            'end_time' => 'required|date',
            'total_minutes' => 'required|integer|min:1'
        ]);

        // Najít aktivní session
        $session = TimeTrackerSession::where('id_user', Auth::id())
            ->where('id_request', $validated['id_request'])
            ->where('completed', false)
            ->whereNull('end_time')
            ->first();

        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'Žádný aktivní časovač nebyl nalezen'
            ], 404);
        }

        // Aktualizovat session
        $session->update([
            'end_time' => Carbon::parse($validated['end_time']),
            'total_minutes' => $validated['total_minutes'],
            'completed' => true
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Časovač byl zastaven',
            'session' => $session
        ]);
    }

    /**
     * Cancel a time tracking session.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function cancelTracking(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id_request' => 'required|exists:requests,id',
        ]);

        // Najít aktivní session
        $session = TimeTrackerSession::where('id_user', Auth::id())
            ->where('id_request', $validated['id_request'])
            ->where('completed', false)
            ->whereNull('end_time')
            ->first();

        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'Žádný aktivní časovač nebyl nalezen'
            ], 404);
        }

        // Smazat session
        $session->delete();

        return response()->json([
            'success' => true,
            'message' => 'Časovač byl zrušen'
        ]);
    }

    /**
     * Get active tracking session for user.
     *
     * @return JsonResponse
     */
    public function getActiveSession(): JsonResponse
    {
        $session = TimeTrackerSession::where('id_user', Auth::id())
            ->where('completed', false)
            ->whereNull('end_time')
            ->with('request')
            ->first();

        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'Žádný aktivní časovač nebyl nalezen'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'session' => $session
        ]);
    }
}
