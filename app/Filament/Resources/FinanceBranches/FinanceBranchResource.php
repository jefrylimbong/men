<?php

namespace App\Filament\Resources\FinanceBranches;

use App\Filament\Resources\FinanceBranches\Pages\CreateFinanceBranch;
use App\Filament\Resources\FinanceBranches\Pages\EditFinanceBranch;
use App\Filament\Resources\FinanceBranches\Pages\ListFinanceBranches;
use App\Filament\Resources\FinanceBranches\Schemas\FinanceBranchForm;
use App\Filament\Resources\FinanceBranches\Tables\FinanceBranchesTable;
use App\Models\FinanceBranch;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class FinanceBranchResource extends Resource
{
    protected static ?string $model = FinanceBranch::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Master Data';

    protected static ?int $navigationSort = 4;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-office';

    protected static ?string $navigationLabel = 'Cabang Finance';

    protected static ?string $modelLabel = 'Cabang Finance';

    protected static ?string $pluralModelLabel = 'Cabang Finance';

    public static function form(Schema $schema): Schema
    {
        return FinanceBranchForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FinanceBranchesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFinanceBranches::route('/'),
            'create' => CreateFinanceBranch::route('/create'),
            'edit' => EditFinanceBranch::route('/{record}/edit'),
        ];
    }
}
