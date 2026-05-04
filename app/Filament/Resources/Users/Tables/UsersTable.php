<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\AndroidActionHistory;
use App\Models\WithdrawalData;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Support\Collection;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('name')
            ->columns([
                TextColumn::make('index')
                    ->label('No')
                    ->rowIndex(),
                ImageColumn::make('avatar')
                    ->circular(),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('username')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->badge()
                    ->separator(','),
                ToggleColumn::make('is_active')
                    ->label('Aktif'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                \Filament\Actions\Action::make('resetPassword')
                    ->label('Reset Pass')
                    ->color('warning')
                    ->icon('heroicon-m-key')
                    ->requiresConfirmation()
                    ->modalHeading('Reset Password')
                    ->modalDescription('Apakah Anda yakin ingin mereset password user ini menjadi 123456?')
                    ->action(function ($record) {
                        $record->update([
                            'password' => \Illuminate\Support\Facades\Hash::make('123456'),
                        ]);

                        Notification::make()
                            ->success()
                            ->title('Berhasil')
                            ->body('Password berhasil direset menjadi 123456.')
                            ->send();
                    }),
                DeleteAction::make()
                    ->before(function (DeleteAction $action, $record) {
                        $hasReferences = WithdrawalData::where('user_id', $record->id)->exists() ||
                                         AndroidActionHistory::where('user_id', $record->id)->exists();

                        if ($hasReferences) {
                            Notification::make()
                                ->danger()
                                ->title('User Gagal Dihapus')
                                ->body('User ini tidak dapat dihapus karena memiliki riwayat data penarikan atau aksi android. Silakan nonaktifkan akun saja.')
                                ->send();

                            $action->cancel();
                        }
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->before(function (DeleteBulkAction $action, Collection $records) {
                            foreach ($records as $record) {
                                $hasReferences = WithdrawalData::where('user_id', $record->id)->exists() ||
                                                 AndroidActionHistory::where('user_id', $record->id)->exists();

                                if ($hasReferences) {
                                    Notification::make()
                                        ->danger()
                                        ->title('Penghapusan Massal Gagal')
                                        ->body("User '{$record->name}' tidak dapat dihapus karena memiliki riwayat data. Silakan nonaktifkan akun saja.")
                                        ->send();

                                    $action->cancel();

                                    return;
                                }
                            }
                        }),
                ]),
            ]);
    }
}
