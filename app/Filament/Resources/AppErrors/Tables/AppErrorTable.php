<?php

namespace App\Filament\Resources\AppErrors\Tables;

use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AppErrorTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d/m/y H:i')
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('User')
                    ->placeholder('Guest')
                    ->searchable(),
                TextColumn::make('device_model')
                    ->label('Merek & Tipe HP')
                    ->searchable()
                    ->description(fn ($record) => $record->device_os),
                TextColumn::make('error_message')
                    ->label('Pesan Error')
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->error_message)
                    ->searchable(),
                SelectColumn::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'fixing' => 'Fixing',
                        'resolved' => 'Resolved',
                        'ignored' => 'Ignored',
                    ])
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'fixing' => 'Fixing',
                        'resolved' => 'Resolved',
                        'ignored' => 'Ignored',
                    ]),
                SelectFilter::make('device_os')
                    ->label('Sistem Operasi')
                    ->options([
                        'android' => 'Android',
                        'ios' => 'iOS',
                    ])
                    ->query(fn ($query, $data) => $query->when($data['value'], fn ($q) => $q->where('device_os', 'like', "%{$data['value']}%"))),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
