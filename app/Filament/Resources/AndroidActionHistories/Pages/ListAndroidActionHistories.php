<?php

namespace App\Filament\Resources\AndroidActionHistories\Pages;

use App\Filament\Resources\AndroidActionHistories\AndroidActionHistoryResource;
use Filament\Resources\Pages\ListRecords;

class ListAndroidActionHistories extends ListRecords
{
    protected static string $resource = AndroidActionHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
