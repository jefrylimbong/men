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
        ]);

        $history = AndroidActionHistory::create([
            'user_id' => $request->user()->id,
            'name' => $validated['name'],
            'action' => $validated['action'],
            'description' => $validated['description'],
            'duration_seconds' => $validated['duration_seconds'] ?? 0,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Action history logged successfully',
            'data' => $history,
        ], 201);
    }
}
