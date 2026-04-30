<?php

namespace App\Filament\Resources\FinanceBranches\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class FinanceBranchForm
{
    public static function configure(Schema $schema, array $params = []): Schema
    {
        $components = [];

        if (! ($params['exclude_code_fin'] ?? false)) {
            $components[] = Select::make('finance_master_id')
                ->label('Finance')
                ->relationship(
                    'financeMaster',
                    'fin_name',
                    fn ($query) => $query->orderBy('fin_name')
                )
                ->searchable()
                ->preload()
                ->required();
        }

        $components[] = Select::make('location_master_id')
            ->label('Lokasi / Cabang')
            ->relationship(
                'locationMaster',
                'name',
                fn ($query) => $query->orderBy('name')
            )
            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->code_loc} - {$record->name}")
            ->searchable()
            ->preload()
            ->required();

        $components[] = Toggle::make('is_active')
            ->label('Status Aktif')
            ->default(true)
            ->required();

        return $schema->components($components);
    }
}
