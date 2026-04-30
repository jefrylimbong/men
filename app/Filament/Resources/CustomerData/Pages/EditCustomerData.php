<?php

namespace App\Filament\Resources\CustomerData\Pages;

use App\Filament\Resources\CustomerData\CustomerDataResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCustomerData extends EditRecord
{
    protected static string $resource = CustomerDataResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
