<?php

namespace App\Filament\Resources\WithdrawalData\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class WithdrawalDataTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('withdrawal_date')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),
                TextColumn::make('customerData.nama')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('customerData.nopol')
                    ->label('No Plat')
                    ->searchable(),
                TextColumn::make('customerData.financeBranch.financeMaster.fin_name')
                    ->label('Finance')
                    ->sortable(),
                TextColumn::make('vendor.nama')
                    ->label('Vendor')
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Lapangan')
                    ->sortable(),
                TextColumn::make('bastk.number')
                    ->label('No BASTK')
                    ->placeholder('Belum ada'),
                TextColumn::make('bailout_amount')
                    ->label('Talangan')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('is_finance_paid')
                    ->label('Cair Finance')
                    ->badge()
                    ->formatStateUsing(fn (bool $state) => $state ? 'Cair' : 'Pending')
                    ->color(fn (bool $state) => $state ? 'success' : 'warning'),
                TextColumn::make('finance_deadline')
                    ->label('Deadline')
                    ->date()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'validated' => 'warning',
                        'paid' => 'success',
                        'canceled' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('vendor_id')
                    ->relationship('vendor', 'nama')
                    ->label('Filter Vendor'),
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'validated' => 'Tervalidasi',
                        'paid' => 'Lunas',
                        'canceled' => 'Dibatalkan',
                    ]),
                SelectFilter::make('deadline_status')
                    ->label('Status Deadline Finance')
                    ->options([
                        'safe' => 'Aman (> 3 Hari)',
                        'warning' => 'Mendekati (1-3 Hari)',
                        'danger' => 'Lewat Deadline',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (empty($data['value'])) {
                            return $query;
                        }

                        $today = now()->startOfDay();
                        $threeDaysFromNow = now()->addDays(3)->endOfDay();

                        return match ($data['value']) {
                            'safe' => $query->where('is_finance_paid', false)
                                ->whereNotNull('finance_deadline')
                                ->whereDate('finance_deadline', '>', $threeDaysFromNow),
                            'warning' => $query->where('is_finance_paid', false)
                                ->whereNotNull('finance_deadline')
                                ->whereDate('finance_deadline', '>=', $today)
                                ->whereDate('finance_deadline', '<=', $threeDaysFromNow),
                            'danger' => $query->where('is_finance_paid', false)
                                ->whereNotNull('finance_deadline')
                                ->whereDate('finance_deadline', '<', $today),
                            default => $query,
                        };
                    }),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
