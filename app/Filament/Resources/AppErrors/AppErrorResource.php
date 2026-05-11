<?php

namespace App\Filament\Resources\AppErrors;

use App\Filament\Resources\AppErrors\Pages\ManageAppErrors;
use App\Filament\Resources\AppErrors\Schemas\AppErrorForm;
use App\Filament\Resources\AppErrors\Tables\AppErrorTable;
use App\Models\AppError;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class AppErrorResource extends Resource
{
    protected static ?string $model = AppError::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Log & Monitor';

    protected static ?int $navigationSort = 10;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-bug-ant';

    protected static ?string $navigationLabel = 'App Errors (Crashes)';

    public static function canAccess(): bool
    {
        return auth()->user()?->type === 'superadmin';
    }

    public static function form(Schema $schema): Schema
    {
        return AppErrorForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AppErrorTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageAppErrors::route('/'),
        ];
    }
}
