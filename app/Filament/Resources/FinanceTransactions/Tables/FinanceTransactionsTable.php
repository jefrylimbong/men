<?php

namespace App\Filament\Resources\FinanceTransactions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FinanceTransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('transaction_date')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),
                TextColumn::make('category')
                    ->label('Kategori')
                    ->searchable(),
                TextColumn::make('debit_type')
                    ->label('Penerima (Debit)')
                    ->formatStateUsing(function ($record) {
                        if (in_array($record->debit_type, ['PT', 'Internal'])) {
                            return $record->debit_type === 'PT' ? 'PT (Internal)' : 'Internal';
                        }
                        $entity = $record->debitEntity;

                        return $entity ? ($record->debit_type.': '.($entity->nama ?? $entity->fin_name)) : '-';
                    }),
                TextColumn::make('credit_type')
                    ->label('Pengirim (Credit)')
                    ->formatStateUsing(function ($record) {
                        if (in_array($record->credit_type, ['PT', 'Internal'])) {
                            return $record->credit_type === 'PT' ? 'PT (Internal)' : 'Internal';
                        }
                        $entity = $record->creditEntity;

                        return $entity ? ($record->credit_type.': '.($entity->nama ?? $entity->fin_name)) : '-';
                    }),
                TextColumn::make('amount')
                    ->label('Nominal')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('description')
                    ->label('Keterangan')
                    ->searchable()
                    ->limit(30),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'completed' => 'success',
                        'canceled' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('transaction_date', 'desc');
    }
}
