<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Hash;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('resetPassword')
                ->label('Reset Password')
                ->color('warning')
                ->icon('heroicon-m-key')
                ->requiresConfirmation()
                ->modalHeading('Reset Password')
                ->modalDescription('Apakah Anda yakin ingin mereset password user ini menjadi 123456?')
                ->action(function ($record) {
                    $record->update([
                        'password' => Hash::make('123456'),
                    ]);

                    Notification::make()
                        ->success()
                        ->title('Berhasil')
                        ->body('Password berhasil direset menjadi 123456.')
                        ->send();
                }),
            DeleteAction::make(),
        ];
    }
}
