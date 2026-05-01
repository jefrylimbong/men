<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\WithdrawalData\WithdrawalDataResource;
use App\Models\FinanceTransaction;
use App\Models\WithdrawalData;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // Calculate PT Cash Balance (Completed transactions only)
        $cashIn = FinanceTransaction::where('debit_type', 'PT')->where('status', 'completed')->sum('amount');
        $cashOut = FinanceTransaction::where('credit_type', 'PT')->where('status', 'completed')->sum('amount');
        $netBalance = $cashIn - $cashOut;

        // Calculate Monthly Cash Flow (Current month)
        $monthlyIn = FinanceTransaction::where('debit_type', 'PT')
            ->where('status', 'completed')
            ->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->sum('amount');
        $monthlyOut = FinanceTransaction::where('credit_type', 'PT')
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

        return [
            Stat::make('Saldo Kas PT', 'Rp '.number_format($netBalance, 0, ',', '.'))
                ->description($netBalance >= 0 ? 'Kas Surplus / Ada' : 'Kas Defisit / Minus')
                ->descriptionIcon($netBalance >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($netBalance >= 0 ? 'success' : 'danger'),

            Stat::make('Arus Kas Bulan Ini', 'Rp '.number_format($monthlyNet, 0, ',', '.'))
                ->description('Pemasukan: Rp '.number_format($monthlyIn, 0, ',', '.'))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color($monthlyNet >= 0 ? 'success' : 'warning'),

            Stat::make('Piutang Finance', 'Rp '.number_format(FinanceTransaction::where('category', 'billing')->where('status', 'pending')->sum('amount'), 0, ',', '.'))
                ->description('Tagihan belum cair')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Hutang Vendor', 'Rp '.number_format(FinanceTransaction::where('category', 'penarikan')->where('status', 'pending')->sum('amount'), 0, ',', '.'))
                ->description('Talangan belum dibayar')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),

            Stat::make('Tagihan Belum Jatuh Tempo', $safeCount.' Unit')
                ->description('Deadline > 3 Hari')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->url(WithdrawalDataResource::getUrl('index', ['tableFilters' => ['deadline_status' => ['value' => 'safe']]])),

            Stat::make('Tagihan Mendekati Deadline', $warningCount.' Unit')
                ->description('Jatuh tempo dalam 3 hari')
                ->descriptionIcon('heroicon-m-exclamation-circle')
                ->color('warning')
                ->url(WithdrawalDataResource::getUrl('index', ['tableFilters' => ['deadline_status' => ['value' => 'warning']]])),

            Stat::make('Tagihan Lewat Deadline', $overdueCount.' Unit')
                ->description('Harus segera ditagih!')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger')
                ->url(WithdrawalDataResource::getUrl('index', ['tableFilters' => ['deadline_status' => ['value' => 'danger']]])),
        ];
    }
}
