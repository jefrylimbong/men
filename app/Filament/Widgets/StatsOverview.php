<?php

namespace App\Filament\Widgets;

use App\Models\FinanceTransaction;
use App\Models\WithdrawalData;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Tarikan Selesai', WithdrawalData::where('status', 'paid')->count())
                ->description('Total unit yang sudah lunas')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Tarikan Dalam Proses', WithdrawalData::whereIn('status', ['pending', 'validated'])->count())
                ->description('Total unit dalam tahap validasi/pembayaran')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Total Revenue (Handling)', 'Rp '.number_format(WithdrawalData::sum('handling_fee'), 0, ',', '.'))
                ->description('Total biaya penanganan unit')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Piutang Finance (Billing)', 'Rp '.number_format(FinanceTransaction::where('category', 'billing')->where('status', 'pending')->sum('amount'), 0, ',', '.'))
                ->description('Dana yang belum cair dari Finance')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('warning'),

            Stat::make('Hutang Vendor', 'Rp '.number_format(FinanceTransaction::where('category', 'penarikan')->where('status', 'pending')->sum('amount'), 0, ',', '.'))
                ->description('Total dana talangan & fee belum dibayar')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger'),
        ];
    }
}
