<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AndroidActionHistory;
use Illuminate\Http\Request;

class ActionHistoryController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|string|max:255',
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'duration_seconds' => 'nullable|integer',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'address' => 'nullable|string',
        ]);

        $history = AndroidActionHistory::create([
            'user_id' => $request->user()->id,
            'name' => $validated['name'],
            'action' => $validated['action'],
            'description' => $validated['description'],
            'duration_seconds' => $validated['duration_seconds'] ?? 0,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'address' => $validated['address'] ?? null,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Action history logged successfully',
            'data' => $history,
        ], 201);
    }
}
