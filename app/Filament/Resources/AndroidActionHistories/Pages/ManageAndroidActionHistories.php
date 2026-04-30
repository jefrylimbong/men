<?php

namespace App\Filament\Resources\AndroidActionHistories\Pages;

use App\Filament\Resources\AndroidActionHistories\AndroidActionHistoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageAndroidActionHistories extends ManageRecords
{
    protected static string $resource = AndroidActionHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
