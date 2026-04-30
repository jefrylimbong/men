<?php

namespace App\Filament\Resources\AndroidActionHistories\Pages;

use App\Filament\Resources\AndroidActionHistories\AndroidActionHistoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAndroidActionHistory extends EditRecord
{
    protected static string $resource = AndroidActionHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
