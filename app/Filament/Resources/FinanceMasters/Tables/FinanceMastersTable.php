<?php

namespace App\Filament\Resources\FinanceMasters\Tables;

use App\Filament\Resources\FinanceBranches\Schemas\FinanceBranchForm;
use App\Models\FinanceBranch;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FinanceMastersTable
{
    public static function configure(Table $table): Table
    {
        $viewBranchesAction = Action::make('viewBranches')
            ->modalHeading(fn ($record) => "Daftar Cabang - {$record->fin_name}")
            ->modalWidth('xl')
            ->infolist([
                RepeatableEntry::make('branches')
                    ->label('Daftar Cabang')
                    ->extraAttributes(['style' => 'max-height: 400px; overflow-y: auto; overflow-x: hidden; padding-right: 10px;'])
                    ->schema([
                        TextEntry::make('locationMaster.code_loc')
                            ->label('Kode Lokasi')
                            ->formatStateUsing(fn ($record) => "{$record->locationMaster->code_loc} - {$record->fin_int}"),
                        TextEntry::make('locationMaster.name')->label('Nama Lokasi'),
                        TextEntry::make('is_active')
                            ->label('Status')
                            ->formatStateUsing(fn ($state) => $state ? 'Aktif' : 'Non-Aktif')
                            ->badge()
                            ->color(fn ($state) => $state ? 'success' : 'danger')
                            ->action(
                                Action::make('toggleStatus')
                                    ->action(function (FinanceBranch $record) {
                                        $record->update(['is_active' => ! $record->is_active]);

                                        Notification::make()
                                            ->title('Status cabang diperbarui')
                                            ->success()
                                            ->send();
                                    })
                            ),
                    ])
                    ->columns(3),
            ])
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Tutup');

        return $table
            ->modifyQueryUsing(fn ($query) => $query->withCount('branches')->with('branches.locationMaster'))
            ->defaultSort('fin_name')
            ->columns([
                TextColumn::make('index')
                    ->label('No')
                    ->rowIndex()
                    ->action($viewBranchesAction),
                TextColumn::make('code_fin')
                    ->label('Kode Finance')
                    ->description(fn ($record) => "{$record->branches_count} Cabang")
                    ->action($viewBranchesAction)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('fin_name')
                    ->label('Nama Finance')
                    ->action($viewBranchesAction)
                    ->searchable()
                    ->sortable(),
                ImageColumn::make('photo')
                    ->label('Foto')
                    ->defaultImageUrl(null),
            ])
            ->actions([
                Action::make('addBranch')
                    ->label('Tambah Cabang')
                    ->icon('heroicon-o-plus-circle')
                    ->color('success')
                    ->form(fn (Schema $schema) => FinanceBranchForm::configure($schema, ['exclude_code_fin' => true])->getComponents())
                    ->action(function (array $data, $record) {
                        FinanceBranch::create([
                            ...$data,
                            'finance_master_id' => $record->id,
                        ]);
                    })
                    ->modalHeading(fn ($record) => "Tambah Cabang untuk {$record->fin_name}")
                    ->modalSubmitActionLabel('Simpan Cabang'),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
