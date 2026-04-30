<?php

namespace App\Filament\Resources\LocationMasters\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class LocationMasterForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code_loc')
                    ->label('Kode Lokasi')
                    ->required()
                    ->maxLength(255),
                TextInput::make('name')
                    ->label('Location')
                    ->required()
                    ->maxLength(255),
            ]);
    }
}
