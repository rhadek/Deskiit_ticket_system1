<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Request as TicketRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

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
}
