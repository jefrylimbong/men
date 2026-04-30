<?php

namespace App\Filament\Resources\WithdrawalData\Pages;

use App\Filament\Resources\WithdrawalData\WithdrawalDataResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListWithdrawalData extends ListRecords
{
    protected static string $resource = WithdrawalDataResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
