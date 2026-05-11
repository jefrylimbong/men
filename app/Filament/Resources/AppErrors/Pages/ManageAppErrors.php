<?php

namespace App\Filament\Resources\AppErrors\Pages;

use App\Filament\Resources\AppErrors\AppErrorResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageAppErrors extends ManageRecords
{
    protected static string $resource = AppErrorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // CreateAction::make(),
        ];
    }
}
