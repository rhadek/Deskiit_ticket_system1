<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Request as TicketRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TimeTrackerController extends Controller
{
    /**
     * Get the name of a request by ID.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function getRequestName($id): JsonResponse
    {
        $request = TicketRequest::find($id);

        if (!$request) {
            return response()->json(['name' => null], 404);
        }

        return response()->json([
            'name' => $request->name,
            'id' => $request->id
        ]);
    }
}
