<?php

namespace App\Filament\Resources\CustomerData\Tables;

use App\Models\FinanceBranch;
use App\Models\FinanceMaster;
use App\Models\LocationMaster;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CustomerDataTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordAction(ViewAction::class)
            ->recordUrl(null)
            ->columns([
                TextColumn::make('index')
                    ->label('No')
                    ->rowIndex(),
                TextColumn::make('nopol')
                    ->label('No. Polisi')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('norak')
                    ->label('No. Rangka')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('nosin')
                    ->label('No. Mesin')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('tipe')
                    ->label('Tipe')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('nama')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('financeBranch.financeMaster.fin_name')
                    ->label('Finance')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('financeBranch.locationMaster.name')
                    ->label('Lokasi')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('tenor')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('ke')
                    ->label('Ke')
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('finance_branch_location')
                    ->form([
                        Select::make('finance_master_id')
                            ->label('Finance')
                            ->options(FinanceMaster::orderBy('fin_name')->pluck('fin_name', 'id'))
                            ->searchable()
                            ->live(),
                        Select::make('finance_branch_id')
                            ->label('Cabang')
                            ->options(function (Get $get) {
                                $financeId = $get('finance_master_id');
                                $query = FinanceBranch::query();

                                if ($financeId) {
                                    $query->where('finance_master_id', $financeId);
                                }

                                return $query->with(['financeMaster', 'locationMaster'])
                                    ->get()
                                    ->map(function ($branch) {
                                        $branch->label = "{$branch->financeMaster?->fin_name} - {$branch->locationMaster?->name}";

                                        return $branch;
                                    })
                                    ->sortBy('label')
                                    ->pluck('label', 'id');
                            })
                            ->searchable()
                            ->live(),
                        Select::make('location_master_id')
                            ->label('Lokasi')
                            ->options(function (Get $get) {
                                $branchId = $get('finance_branch_id');
                                if ($branchId) {
                                    $branch = FinanceBranch::find($branchId);
                                    if ($branch) {
                                        return [$branch->location_master_id => $branch->locationMaster?->name];
                                    }
                                }

                                return LocationMaster::orderBy('name')->pluck('name', 'id');
                            })
                            ->searchable(),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['finance_master_id'], function ($query, $financeId) {
                                $query->whereHas('financeBranch', fn ($q) => $q->where('finance_master_id', $financeId));
                            })
                            ->when($data['finance_branch_id'], function ($query, $branchId) {
                                $query->where('finance_branch_id', $branchId);
                            })
                            ->when($data['location_master_id'], function ($query, $locationId) {
                                $query->whereHas('financeBranch', fn ($q) => $q->where('location_master_id', $locationId));
                            });
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['finance_master_id'] ?? null) {
                            $indicators[] = 'Finance: '.FinanceMaster::find($data['finance_master_id'])?->fin_name;
                        }
                        if ($data['finance_branch_id'] ?? null) {
                            $branch = FinanceBranch::find($data['finance_branch_id']);
                            $indicators[] = 'Cabang: '."{$branch?->financeMaster?->fin_name} - {$branch?->locationMaster?->name}";
                        }
                        if ($data['location_master_id'] ?? null) {
                            $indicators[] = 'Lokasi: '.LocationMaster::find($data['location_master_id'])?->name;
                        }

                        return $indicators;
                    }),
                TernaryFilter::make('is_active')
                    ->label('Status Aktif'),
            ])
            ->actions([
                ViewAction::make()
                    ->modalHeading('Detail Customer'),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
