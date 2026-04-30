<?php

namespace App\Filament\Resources\WithdrawalData\Pages;

use App\Filament\Resources\WithdrawalData\WithdrawalDataResource;
use App\Models\BastkRegister;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditWithdrawalData extends EditRecord
{
    protected static string $resource = WithdrawalDataResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $data = $this->form->getRawState();

        if ($data['is_bastk_ready'] ?? false) {
            if (! empty($data['bastk_id'])) {
                // Lepaskan BASTK lama jika ada yang berbeda
                BastkRegister::where('withdrawal_data_id', $this->record->id)
                    ->where('id', '!=', $data['bastk_id'])
                    ->update(['withdrawal_data_id' => null]);

                // Hubungkan BASTK baru
                BastkRegister::where('id', $data['bastk_id'])->update([
                    'withdrawal_data_id' => $this->record->id,
                    'status' => $data['bastk_status'] ?? false,
                    'photos' => $data['bastk_photos'] ?? [],
                    'files' => $data['bastk_files'] ?? [],
                ]);
            }
        } else {
            // Jika BASTK tidak tersedia lagi, lepaskan relasi
            BastkRegister::where('withdrawal_data_id', $this->record->id)
                ->update(['withdrawal_data_id' => null]);
        }
    }
}
