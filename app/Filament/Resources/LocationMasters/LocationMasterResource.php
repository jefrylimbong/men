<?php

namespace App\Filament\Resources\LocationMasters;

use App\Filament\Resources\LocationMasters\Pages\CreateLocationMaster;
use App\Filament\Resources\LocationMasters\Pages\EditLocationMaster;
use App\Filament\Resources\LocationMasters\Pages\ListLocationMasters;
use App\Filament\Resources\LocationMasters\Schemas\LocationMasterForm;
use App\Filament\Resources\LocationMasters\Tables\LocationMastersTable;
use App\Models\LocationMaster;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class LocationMasterResource extends Resource
{
    protected static ?string $model = LocationMaster::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Master Data';

    protected static ?int $navigationSort = 5;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-map-pin';

    protected static ?string $navigationLabel = 'Location';

    public static function form(Schema $schema): Schema
    {
        return LocationMasterForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LocationMastersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLocationMasters::route('/'),
            'create' => CreateLocationMaster::route('/create'),
            'edit' => EditLocationMaster::route('/{record}/edit'),
        ];
    }
}
