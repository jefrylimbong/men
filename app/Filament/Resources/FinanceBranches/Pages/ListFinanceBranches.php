<?php

namespace App\Filament\Resources\FinanceBranches\Pages;

use App\Filament\Resources\FinanceBranches\FinanceBranchResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFinanceBranches extends ListRecords
{
    protected static string $resource = FinanceBranchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
