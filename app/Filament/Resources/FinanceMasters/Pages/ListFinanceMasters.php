<?php

namespace App\Filament\Resources\FinanceMasters\Pages;

use App\Filament\Resources\FinanceMasters\FinanceMasterResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFinanceMasters extends ListRecords
{
    protected static string $resource = FinanceMasterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
