<?php

namespace App\Filament\Resources\FinanceMasters\Pages;

use App\Filament\Resources\FinanceMasters\FinanceMasterResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFinanceMaster extends EditRecord
{
    protected static string $resource = FinanceMasterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
