<?php

namespace App\Filament\Resources\WithdrawalData\Pages;

use App\Filament\Resources\WithdrawalData\WithdrawalDataResource;
use App\Models\BastkRegister;
use Filament\Resources\Pages\CreateRecord;

class CreateWithdrawalData extends CreateRecord
{
    protected static string $resource = WithdrawalDataResource::class;

    protected function afterCreate(): void
    {
        $data = $this->form->getRawState();

        if (($data['is_bastk_ready'] ?? false) && ! empty($data['bastk_id'])) {
            BastkRegister::where('id', $data['bastk_id'])->update([
                'withdrawal_data_id' => $this->record->id,
                'status' => $data['bastk_status'] ?? false,
                'photos' => $data['bastk_photos'] ?? [],
                'files' => $data['bastk_files'] ?? [],
            ]);
        }
    }
}
