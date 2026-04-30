<?php

namespace App\Filament\Resources\BastkRegisters;

use App\Filament\Resources\BastkRegisters\Pages\CreateBastkRegister;
use App\Filament\Resources\BastkRegisters\Pages\EditBastkRegister;
use App\Filament\Resources\BastkRegisters\Pages\ListBastkRegisters;
use App\Filament\Resources\BastkRegisters\Schemas\BastkRegisterForm;
use App\Filament\Resources\BastkRegisters\Tables\BastkRegistersTable;
use App\Models\BastkRegister;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class BastkRegisterResource extends Resource
{
    protected static ?string $model = BastkRegister::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Operasional';

    protected static ?int $navigationSort = 2;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'BASTK Register';

    public static function form(Schema $schema): Schema
    {
        return BastkRegisterForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BastkRegistersTable::configure($table);
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
            'index' => ListBastkRegisters::route('/'),
            'create' => CreateBastkRegister::route('/create'),
            'edit' => EditBastkRegister::route('/{record}/edit'),
        ];
    }
}
