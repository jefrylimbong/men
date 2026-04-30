<?php

namespace App\Filament\Resources\BastkRegisters\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class BastkRegistersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('number')
                    ->label('No BASTK')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('withdrawalData.customerData.nopol')
                    ->label('Digunakan Oleh')
                    ->placeholder('Belum digunakan')
                    ->description(fn ($record) => $record->withdrawalData ? $record->withdrawalData->customerData->nama : null),
                ToggleColumn::make('status')
                    ->label('Status Aktif'),
                TextColumn::make('created_at')
                    ->label('Tanggal Input')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make()
                    ->before(function (DeleteAction $action, $record) {
                        if ($record->withdrawal_data_id) {
                            Notification::make()
                                ->danger()
                                ->title('BASTK Gagal Dihapus')
                                ->body('BASTK ini sedang digunakan pada data penarikan.')
                                ->send();

                            $action->cancel();
                        }
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
