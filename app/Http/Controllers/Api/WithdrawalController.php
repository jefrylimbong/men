<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FinanceMaster;
use App\Models\Vendor;
use App\Models\WithdrawalData;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    /**
     * Get list of withdrawals for the authenticated user.
     */
    public function index(Request $request)
    {
        $withdrawals = WithdrawalData::with(['finance', 'vendor'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(20);

        return response()->json([
            'status' => 'success',
            'data' => $withdrawals,
        ]);
    }

    /**
     * Store a new withdrawal record from field worker.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_data_id' => 'required|exists:customer_data,id',
            'withdrawal_date' => 'nullable|date',
            'status' => 'required|string|max:255',
            'vendor_id' => 'nullable|exists:vendors,id',
            'bailout_amount' => 'nullable|numeric',
        ]);

        $withdrawal = WithdrawalData::create([
            'customer_data_id' => $validated['customer_data_id'],
            'user_id' => $request->user()->id,
            'vendor_id' => $validated['vendor_id'] ?? null,
            'status' => $validated['status'],
            'withdrawal_date' => $validated['withdrawal_date'] ?? now(),
            'bailout_amount' => $validated['bailout_amount'] ?? 0,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Data penarikan berhasil disimpan',
            'data' => $withdrawal,
        ], 201);
    }

    /**
     * Get list of Finance and Vendors for dropdowns in Flutter.
     */
    public function getMasters()
    {
        $finances = FinanceMaster::select('id', 'fin_name as name')->where('is_active', true)->get();
        $vendors = Vendor::select('id', 'nama as name')->where('is_active', true)->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'finances' => $finances,
                'vendors' => $vendors,
            ],
        ]);
    }
}
