<?php

namespace App\Filament\Resources\FinanceMasters;

use App\Filament\Resources\FinanceMasters\Pages\CreateFinanceMaster;
use App\Filament\Resources\FinanceMasters\Pages\EditFinanceMaster;
use App\Filament\Resources\FinanceMasters\Pages\ListFinanceMasters;
use App\Filament\Resources\FinanceMasters\Schemas\FinanceMasterForm;
use App\Filament\Resources\FinanceMasters\Tables\FinanceMastersTable;
use App\Models\FinanceMaster;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class FinanceMasterResource extends Resource
{
    protected static ?string $model = FinanceMaster::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Master Data';

    protected static ?int $navigationSort = 3;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-circle-stack';

    protected static ?string $navigationLabel = 'Finance';

    public static function form(Schema $schema): Schema
    {
        return FinanceMasterForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FinanceMastersTable::configure($table);
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
            'index' => ListFinanceMasters::route('/'),
            'create' => CreateFinanceMaster::route('/create'),
            'edit' => EditFinanceMaster::route('/{record}/edit'),
        ];
    }
}
