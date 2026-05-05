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
        if (! in_array($request->user()->type, ['admin', 'superadmin'])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Calculate PT Cash Balance (Completed transactions only)
        $cashIn = \App\Models\FinanceTransaction::where('debit_type', 'PT')->where('status', 'completed')->sum('amount');
        $cashOut = \App\Models\FinanceTransaction::where('credit_type', 'PT')->where('status', 'completed')->sum('amount');
        $netBalance = $cashIn - $cashOut;

        // Calculate Monthly Cash Flow (Current month)
        $monthlyIn = \App\Models\FinanceTransaction::where('debit_type', 'PT')
            ->where('status', 'completed')
            ->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->sum('amount');
        $monthlyOut = \App\Models\FinanceTransaction::where('credit_type', 'PT')
            ->where('status', 'completed')
            ->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->sum('amount');
        $monthlyNet = $monthlyIn - $monthlyOut;

        // Calculate Finance Deadline Stats
        $today = now()->startOfDay();
        $threeDaysFromNow = now()->addDays(3)->endOfDay();

        $overdueCount = WithdrawalData::where('is_finance_paid', false)
            ->whereNotNull('finance_deadline')
            ->whereDate('finance_deadline', '<', $today)
            ->count();

        $warningCount = WithdrawalData::where('is_finance_paid', false)
            ->whereNotNull('finance_deadline')
            ->whereDate('finance_deadline', '>=', $today)
            ->whereDate('finance_deadline', '<=', $threeDaysFromNow)
            ->count();

        $safeCount = WithdrawalData::where('is_finance_paid', false)
            ->whereNotNull('finance_deadline')
            ->whereDate('finance_deadline', '>', $threeDaysFromNow)
            ->count();

        // Trend Penarikan (Last 6 Months)
        $trend = WithdrawalData::select(
            DB::raw('COUNT(*) as count'),
            DB::raw("DATE_FORMAT(withdrawal_date, '%M') as month"),
            DB::raw('MONTH(withdrawal_date) as month_num')
        )
        ->groupBy('month', 'month_num')
        ->orderBy('month_num', 'asc')
        ->where('withdrawal_date', '>=', now()->subMonths(5)->startOfMonth())
        ->get();

        // Status Distribution
        $statusDistribution = WithdrawalData::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        // Recent Withdrawals (Today Only)
        $recentWithdrawals = WithdrawalData::with(['customerData', 'vendor'])
            ->whereDate('created_at', now()->today())
            ->latest()
            ->get()
            ->map(function ($w) {
                return [
                    'id' => $w->id,
                    'date' => $w->withdrawal_date ? $w->withdrawal_date->format('M d, Y') : '-',
                    'customer' => $w->customerData->nama ?? '-',
                    'nopol' => $w->customerData->nopol ?? '-',
                    'vendor' => $w->vendor->nama ?? '-',
                    'status' => $w->status,
                ];
            });

        $stats = [
            'saldo_kas' => [
                'value' => $netBalance,
                'is_surplus' => $netBalance >= 0,
            ],
            'arus_kas' => [
                'value' => $monthlyNet,
                'pemasukan' => $monthlyIn,
                'is_surplus' => $monthlyNet >= 0,
            ],
            'piutang_finance' => \App\Models\FinanceTransaction::where('category', 'billing')->where('status', 'pending')->sum('amount'),
            'hutang_vendor' => \App\Models\FinanceTransaction::where('category', 'penarikan')->where('status', 'pending')->sum('amount'),
            'tagihan_safe' => $safeCount,
            'tagihan_warning' => $warningCount,
            'tagihan_danger' => $overdueCount,
            'trend' => $trend,
            'status_distribution' => $statusDistribution,
            'recent_withdrawals' => $recentWithdrawals,
        ];

        return response()->json([
            'status' => 'success',
            'data' => $stats,
        ]);
    }
}
