<?php

namespace App\Filament\Resources\AndroidActionHistories;

use App\Filament\Resources\AndroidActionHistories\Pages\ListAndroidActionHistories;
use App\Models\AndroidActionHistory;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AndroidActionHistoryResource extends Resource
{
    protected static ?string $model = AndroidActionHistory::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Log & Monitor';

    protected static ?int $navigationSort = 1;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationLabel = 'Android Action History';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name'),
                TextInput::make('action'),
                Textarea::make('description'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('index')
                    ->label('No')
                    ->rowIndex(),
                TextColumn::make('user.name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('action')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('description')
                    ->limit(30),
                TextColumn::make('address')
                    ->label('Location/Address')
                    ->description(fn ($record) => $record->latitude ? "{$record->latitude}, {$record->longitude}" : null)
                    ->wrap()
                    ->limit(50),
                TextColumn::make('duration_seconds')
                    ->label('Duration')
                    ->formatStateUsing(fn ($state) => $state ? "{$state}s" : '-'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Read only usually for history
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAndroidActionHistories::route('/'),
        ];
    }
}
