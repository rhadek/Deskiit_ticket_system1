<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Request as TicketRequest;
use App\Models\TimeTrackerSession;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

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
        try {
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
        } catch (\Exception $e) {
            Log::error('Error in getRequestName: ' . $e->getMessage());
            return response()->json([
                'name' => null,
                'message' => 'Error getting request details: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Start a time tracking session.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function startTracking(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'id_request' => 'required|exists:requests,id',
                'start_time' => 'required|date'
            ]);

            // Získáme požadavek
            $ticketRequest = TicketRequest::find($validated['id_request']);
            if (!$ticketRequest) {
                return response()->json([
                    'success' => false,
                    'message' => 'Požadavek nebyl nalezen'
                ], 404);
            }

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
                'completed' => false,
                'report_created' => false
            ]);

            $requestUpdated = false;

            // Aktualizovat stav požadavku na "V řešení" (state = 2), pokud byl "Nový" (state = 1)
            if ($ticketRequest->state == 1) {
                $ticketRequest->update(['state' => 2]);
                $requestUpdated = true;
            }

            return response()->json([
                'success' => true,
                'message' => 'Časovač byl spuštěn',
                'session' => $session,
                'request_updated' => $requestUpdated
            ]);
        } catch (\Exception $e) {
            Log::error('Error in startTracking: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Při spouštění časovače došlo k chybě: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Stop a time tracking session.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function stopTracking(Request $request): JsonResponse
    {
        try {
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
        } catch (\Exception $e) {
            Log::error('Error in stopTracking: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Při zastavení časovače došlo k chybě: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel a time tracking session.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function cancelTracking(Request $request): JsonResponse
    {
        try {
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
        } catch (\Exception $e) {
            Log::error('Error in cancelTracking: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Při rušení časovače došlo k chybě: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get active tracking session for user.
     *
     * @return JsonResponse
     */
    public function getActiveSession(): JsonResponse
    {
        try {
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
        } catch (\Exception $e) {
            Log::error('Error in getActiveSession: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Při získávání aktivní session došlo k chybě: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check session status.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function checkSession($id): JsonResponse
    {
        try {
            $session = TimeTrackerSession::find($id);

            if (!$session) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session not found'
                ], 404);
            }

            // Přidáme timestamp pro zamezení cachování
            return response()->json([
                'success' => true,
                'session' => $session,
                'timestamp' => now()->timestamp
            ]);
        } catch (\Exception $e) {
            Log::error('Error in checkSession: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Při kontrole session došlo k chybě: ' . $e->getMessage()
            ], 500);
        }
    }
}
