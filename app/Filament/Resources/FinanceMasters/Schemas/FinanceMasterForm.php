<?php

namespace App\Filament\Resources\FinanceMasters\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class FinanceMasterForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code_fin')
                    ->label('Kode Finance')
                    ->required()
                    ->maxLength(255),
                TextInput::make('fin_name')
                    ->label('Nama Finance')
                    ->required()
                    ->maxLength(255),
                FileUpload::make('photo')
                    ->label('Foto')
                    ->image()
                    ->directory('finance-photos')
                    ->nullable(),
            ]);
    }
}
