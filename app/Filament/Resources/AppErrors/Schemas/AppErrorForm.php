<?php

namespace App\Filament\Resources\AppErrors\Schemas;

use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AppErrorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detail Laporan Error')
                    ->description('Informasi teknis terkait kesalahan yang terjadi pada aplikasi.')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Placeholder::make('user_name')
                                    ->label('User')
                                    ->content(fn ($record) => $record?->user?->name ?? 'Guest'),
                                Placeholder::make('created_at')
                                    ->label('Waktu Kejadian')
                                    ->content(fn ($record) => $record?->created_at?->format('d M Y H:i:s') ?? '-'),
                                Select::make('status')
                                    ->label('Status Perbaikan')
                                    ->options([
                                        'pending' => 'Pending (Belum Dicek)',
                                        'fixing' => 'Sedang Diperbaiki',
                                        'resolved' => 'Selesai / Teratasi',
                                        'ignored' => 'Diabaikan / Bukan Bug',
                                    ])
                                    ->required()
                                    ->selectablePlaceholder(false),
                            ]),

                        Grid::make(3)
                            ->schema([
                                TextInput::make('device_model')
                                    ->label('Merek & Tipe HP')
                                    ->readonly(),
                                TextInput::make('device_os')
                                    ->label('Sistem Operasi')
                                    ->readonly(),
                                TextInput::make('app_version')
                                    ->label('Versi Aplikasi')
                                    ->readonly(),
                            ]),

                        TextInput::make('page_path')
                            ->label('Lokasi Halaman')
                            ->readonly()
                            ->columnSpanFull(),

                        Textarea::make('error_message')
                            ->label('Pesan Error')
                            ->rows(3)
                            ->readonly()
                            ->columnSpanFull(),

                        Textarea::make('stack_trace')
                            ->label('Stack Trace (Teknis)')
                            ->rows(10)
                            ->readonly()
                            ->extraAttributes(['class' => 'font-mono text-xs'])
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
