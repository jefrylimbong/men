<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppError;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function logError(Request $request)
    {
        $request->validate([
            'error_message' => 'required|string',
        ]);

        try {
            AppError::create([
                'user_id' => $request->user()?->id,
                'device_model' => $request->input('device_model'),
                'device_os' => $request->input('device_os'),
                'app_version' => $request->input('app_version'),
                'error_message' => $request->input('error_message'),
                'stack_trace' => $request->input('stack_trace'),
                'page_path' => $request->input('page_path'),
            ]);

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
