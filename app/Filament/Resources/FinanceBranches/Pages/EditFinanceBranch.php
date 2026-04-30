<?php

namespace App\Filament\Resources\FinanceBranches\Pages;

use App\Filament\Resources\FinanceBranches\FinanceBranchResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFinanceBranch extends EditRecord
{
    protected static string $resource = FinanceBranchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
