<?php

namespace App\Filament\Resources\FinanceTransactions;

use App\Filament\Resources\FinanceTransactions\Pages\CreateFinanceTransaction;
use App\Filament\Resources\FinanceTransactions\Pages\EditFinanceTransaction;
use App\Filament\Resources\FinanceTransactions\Pages\ListFinanceTransactions;
use App\Filament\Resources\FinanceTransactions\Schemas\FinanceTransactionForm;
use App\Filament\Resources\FinanceTransactions\Tables\FinanceTransactionsTable;
use App\Models\FinanceTransaction;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class FinanceTransactionResource extends Resource
{
    protected static ?string $model = FinanceTransaction::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCurrencyDollar;

    protected static \UnitEnum|string|null $navigationGroup = 'Keuangan';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Transaksi Kas';

    protected static ?string $recordTitleAttribute = 'description';

    public static function form(Schema $schema): Schema
    {
        return FinanceTransactionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FinanceTransactionsTable::configure($table);
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
            'index' => ListFinanceTransactions::route('/'),
            'create' => CreateFinanceTransaction::route('/create'),
            'edit' => EditFinanceTransaction::route('/{record}/edit'),
        ];
    }
}
