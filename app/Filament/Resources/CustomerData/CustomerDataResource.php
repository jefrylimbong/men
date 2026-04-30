<?php

namespace App\Filament\Resources\CustomerData;

use App\Filament\Resources\CustomerData\Pages\CreateCustomerData;
use App\Filament\Resources\CustomerData\Pages\EditCustomerData;
use App\Filament\Resources\CustomerData\Pages\ListCustomerData;
use App\Filament\Resources\CustomerData\Schemas\CustomerDataForm;
use App\Filament\Resources\CustomerData\Tables\CustomerDataTable;
use App\Models\CustomerData;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class CustomerDataResource extends Resource
{
    protected static ?string $model = CustomerData::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Master Data';

    protected static ?int $navigationSort = 1;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Customer Data';

    public static function form(Schema $schema): Schema
    {
        return CustomerDataForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CustomerDataTable::configure($table);
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
            'index' => ListCustomerData::route('/'),
            'create' => CreateCustomerData::route('/create'),
            'edit' => EditCustomerData::route('/{record}/edit'),
        ];
    }
}
