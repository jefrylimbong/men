<?php

namespace App\Filament\Resources\WithdrawalData\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

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
