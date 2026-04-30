<?php

namespace App\Filament\Resources\BastkRegisters\Pages;

use App\Filament\Resources\BastkRegisters\BastkRegisterResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBastkRegister extends EditRecord
{
    protected static string $resource = BastkRegisterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
