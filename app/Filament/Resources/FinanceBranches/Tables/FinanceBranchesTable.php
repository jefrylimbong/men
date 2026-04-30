<?php

namespace App\Filament\Resources\FinanceBranches\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class FinanceBranchesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query
                ->join('finance_masters', 'finance_branches.finance_master_id', '=', 'finance_masters.id')
                ->join('location_masters', 'finance_branches.location_master_id', '=', 'location_masters.id')
                ->select('finance_branches.*')
                ->orderBy('finance_masters.fin_name')
                ->orderBy('location_masters.name')
            )
            ->groups([
                Group::make('financeMaster.fin_name')
                    ->label('Finance')
                    ->collapsible(),
            ])
            ->defaultGroup('financeMaster.fin_name')
            ->columns([
                TextColumn::make('index')
                    ->label('No')
                    ->rowIndex(),
                TextColumn::make('financeMaster.fin_name')
                    ->label('Finance')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('locationMaster.code_loc')
                    ->label('Lokasi / Cabang')
                    ->description(fn ($record) => $record->locationMaster?->name)
                    ->searchable()
                    ->sortable(),
                ToggleColumn::make('is_active')
                    ->label('Aktif'),
            ])
            ->filters([
                //
            ])
            ->actions([
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
