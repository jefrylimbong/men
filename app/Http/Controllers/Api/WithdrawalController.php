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
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $query = WithdrawalData::with(['customerData', 'vendor'])
            ->where('user_id', $request->user()->id);

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate.' 00:00:00', $endDate.' 23:59:59']);
        } else {
            $query->whereDate('created_at', now()->today());
        }

        $withdrawals = $query->latest()->get(); // Menggunakan get() agar semua muncul di daftar tugas harian

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
            'status' => 'pending', // Paksa status jadi pending saat masuk dari aplikasi
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
     * Update withdrawal status.
     */
    public function update(Request $request, $id)
    {
        $withdrawal = WithdrawalData::where('user_id', $request->user()->id)->findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|string|max:255',
        ]);

        $withdrawal->update([
            'status' => $validated['status'],
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Status penarikan berhasil diperbarui',
            'data' => $withdrawal,
        ]);
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

    public function destroy($id)
    {
        $withdrawal = WithdrawalData::where('user_id', auth()->id())->findOrFail($id);
        $withdrawal->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Tugas berhasil dihapus',
        ]);
    }
}
