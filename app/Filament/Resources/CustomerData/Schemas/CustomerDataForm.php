<?php

namespace App\Filament\Resources\CustomerData\Schemas;

use App\Models\FinanceBranch;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CustomerDataForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nopol')
                    ->label('No. Polisi')
                    ->required(),
                TextInput::make('nama')
                    ->label('Nama Customer')
                    ->required(),
                TextInput::make('norak')
                    ->label('No. Rangka'),
                TextInput::make('nosin')
                    ->label('No. Mesin'),
                TextInput::make('tipe')
                    ->label('Tipe Kendaraan'),
                TextInput::make('tenor'),
                TextInput::make('ke')
                    ->label('Angsuran Ke'),
                TextInput::make('od')
                    ->label('Overdue (Hari)'),
                TextInput::make('ph')
                    ->label('PH'),
                Select::make('finance_branch_id')
                    ->label('Cabang Finance')
                    ->options(function () {
                        return FinanceBranch::with(['financeMaster', 'locationMaster'])
                            ->get()
                            ->sortBy(fn ($branch) => ($branch->financeMaster?->fin_name ?? '').' '.($branch->locationMaster?->name ?? ''))
                            ->groupBy(fn ($branch) => $branch->financeMaster?->fin_name ?? 'Lainnya')
                            ->map(function ($group) {
                                return $group->mapWithKeys(function ($branch) {
                                    $label = ($branch->financeMaster?->fin_name ?? '').' - '.($branch->locationMaster?->name ?? '');

                                    return [$branch->id => $label];
                                })->toArray();
                            })->toArray();
                    })
                    ->searchable()
                    ->preload(),
                Textarea::make('alamat')
                    ->label('Alamat')
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
            ]);
    }
}
