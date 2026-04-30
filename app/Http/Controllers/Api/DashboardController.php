<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CustomerData;
use App\Models\User;
use App\Models\Vendor;
use App\Models\WithdrawalData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Pastikan hanya admin/superadmin yang bisa akses (opsional karena rute akan diproteksi middleware)
        if (! in_array($request->user()->user_type, ['admin', 'superadmin'])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $stats = [
            'total_vehicles' => CustomerData::count(),
            'total_withdrawals' => WithdrawalData::count(),
            'withdrawals_today' => WithdrawalData::whereDate('created_at', now())->count(),
            'total_vendors' => Vendor::count(),
            'total_field_workers' => User::where('user_type', 'user')->count(),

            // Statistik Penarikan per Status
            'status_counts' => WithdrawalData::select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->get(),

            // Ringkasan Keuangan (Estimasi Profit PT)
            'financial' => [
                'total_estimated_payout' => WithdrawalData::sum('estimated_payout'),
                'total_bailout' => WithdrawalData::sum('bailout_amount'),
                'total_handling_fee' => WithdrawalData::sum('handling_fee'),
            ],
        ];

        return response()->json([
            'status' => 'success',
            'data' => $stats,
        ]);
    }
}
