<?php

namespace App\Filament\Widgets;

use App\Models\WithdrawalData;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Illuminate\Support\Facades\DB;

class MonthlyWithdrawalsChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 1;

    protected ?string $heading = 'Trend Penarikan Bulanan';

    protected function getData(): array
    {
        // If Flowframe/Trend is not installed, we use raw query
        $data = WithdrawalData::select(
            DB::raw('count(*) as aggregate'),
            DB::raw('MONTH(withdrawal_date) as month')
        )
            ->where('withdrawal_date', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $chartData = [];
        $chartLabels = [];

        foreach ($data as $item) {
            $monthName = date('F', mktime(0, 0, 0, $item->month, 10));
            $chartLabels[] = $monthName;
            $chartData[] = $item->aggregate;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Unit Ditarik',
                    'data' => $chartData,
                    'fill' => 'start',
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                ],
            ],
            'labels' => $chartLabels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
