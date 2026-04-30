<?php

namespace App\Filament\Widgets;

use App\Models\WithdrawalData;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class WithdrawalChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 1;

    protected ?string $heading = 'Status Penarikan';

    protected function getData(): array
    {
        $data = WithdrawalData::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $labels = [
            'pending' => 'Pending',
            'validated' => 'Tervalidasi',
            'paid' => 'Lunas',
            'canceled' => 'Dibatalkan',
        ];

        $chartData = [];
        $chartLabels = [];
        $colors = [
            'gray', // pending
            '#FBBF24', // validated (amber)
            '#10B981', // paid (emerald)
            '#EF4444', // canceled (red)
        ];

        foreach ($labels as $key => $label) {
            $chartLabels[] = $label;
            $chartData[] = $data[$key] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Unit',
                    'data' => $chartData,
                    'backgroundColor' => ['#94a3b8', '#FBBF24', '#10B981', '#EF4444'],
                ],
            ],
            'labels' => $chartLabels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
