<?php

namespace App\Filament\Widgets;

use App\Models\WithdrawalData;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestWithdrawals extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Penarikan Terbaru';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                WithdrawalData::query()->latest()->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('withdrawal_date')
                    ->label('Tanggal')
                    ->date(),
                Tables\Columns\TextColumn::make('customerData.nama')
                    ->label('Customer'),
                Tables\Columns\TextColumn::make('customerData.nopol')
                    ->label('No Plat'),
                Tables\Columns\TextColumn::make('vendor.nama')
                    ->label('Vendor'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'validated' => 'warning',
                        'paid' => 'success',
                        'canceled' => 'danger',
                        default => 'gray',
                    }),
            ]);
    }
}
