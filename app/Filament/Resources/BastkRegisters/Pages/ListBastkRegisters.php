<?php

namespace App\Filament\Resources\BastkRegisters\Pages;

use App\Filament\Resources\BastkRegisters\BastkRegisterResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBastkRegisters extends ListRecords
{
    protected static string $resource = BastkRegisterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
