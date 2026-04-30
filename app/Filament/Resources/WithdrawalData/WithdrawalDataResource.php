<?php

namespace App\Filament\Resources\WithdrawalData;

use App\Filament\Resources\WithdrawalData\Pages\CreateWithdrawalData;
use App\Filament\Resources\WithdrawalData\Pages\EditWithdrawalData;
use App\Filament\Resources\WithdrawalData\Pages\ListWithdrawalData;
use App\Filament\Resources\WithdrawalData\Schemas\WithdrawalDataForm;
use App\Filament\Resources\WithdrawalData\Tables\WithdrawalDataTable;
use App\Models\WithdrawalData;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class WithdrawalDataResource extends Resource
{
    protected static ?string $model = WithdrawalData::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Operasional';

    protected static ?int $navigationSort = 1;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Withdrawal Data';

    public static function form(Schema $schema): Schema
    {
        return WithdrawalDataForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WithdrawalDataTable::configure($table);
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
            'index' => ListWithdrawalData::route('/'),
            'create' => CreateWithdrawalData::route('/create'),
            'edit' => EditWithdrawalData::route('/{record}/edit'),
        ];
    }
}
