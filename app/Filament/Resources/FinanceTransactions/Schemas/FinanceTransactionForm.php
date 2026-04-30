<?php

namespace App\Filament\Resources\FinanceTransactions\Schemas;

use App\Models\FinanceMaster;
use App\Models\Vendor;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FinanceTransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detail Transaksi')
                    ->schema([
                        DatePicker::make('transaction_date')
                            ->label('Tanggal Transaksi')
                            ->required()
                            ->default(now()),
                        Select::make('category')
                            ->label('Kategori')
                            ->options([
                                'penarikan' => 'Biaya Penarikan',
                                'operational' => 'Operasional Kantor',
                                'billing' => 'Penagihan Finance',
                                'payment' => 'Pembayaran Vendor',
                                'other' => 'Lainnya',
                            ])
                            ->required(),
                        TextInput::make('amount')
                            ->label('Jumlah (Nominal)')
                            ->required()
                            ->numeric()
                            ->prefix('Rp'),
                        TextInput::make('description')
                            ->label('Keterangan')
                            ->columnSpanFull(),
                    ])->columns(3),

                Section::make('Pihak Terkait (Arus Uang)')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Fieldset::make('Penerima / Debit (Uang Masuk)')
                                    ->schema([
                                        Select::make('debit_type')
                                            ->label('Jenis Entitas')
                                            ->options([
                                                'PT' => 'PT (Internal)',
                                                'Vendor' => 'Vendor',
                                                'Finance' => 'Perusahaan Finance',
                                            ])
                                            ->reactive()
                                            ->required(),
                                        Select::make('debit_id')
                                            ->label('Pilih Entitas')
                                            ->options(function (callable $get) {
                                                $type = $get('debit_type');
                                                if ($type === 'Vendor') {
                                                    return Vendor::pluck('nama', 'id');
                                                }
                                                if ($type === 'Finance') {
                                                    return FinanceMaster::pluck('fin_name', 'id');
                                                }

                                                return ['0' => 'PT Internal'];
                                            })
                                            ->searchable()
                                            ->hidden(fn (callable $get) => ! $get('debit_type')),
                                    ]),
                                Fieldset::make('Pengirim / Credit (Uang Keluar)')
                                    ->schema([
                                        Select::make('credit_type')
                                            ->label('Jenis Entitas')
                                            ->options([
                                                'PT' => 'PT (Internal)',
                                                'Vendor' => 'Vendor',
                                                'Finance' => 'Perusahaan Finance',
                                            ])
                                            ->reactive()
                                            ->required(),
                                        Select::make('credit_id')
                                            ->label('Pilih Entitas')
                                            ->options(function (callable $get) {
                                                $type = $get('credit_type');
                                                if ($type === 'Vendor') {
                                                    return Vendor::pluck('nama', 'id');
                                                }
                                                if ($type === 'Finance') {
                                                    return FinanceMaster::pluck('fin_name', 'id');
                                                }

                                                return ['0' => 'PT Internal'];
                                            })
                                            ->searchable()
                                            ->hidden(fn (callable $get) => ! $get('credit_type')),
                                    ]),
                            ]),
                    ]),

                Section::make('Status & Referensi')
                    ->schema([
                        Select::make('reference_id')
                            ->label('Referensi Penarikan')
                            ->relationship('reference', 'plate_number')
                            ->searchable()
                            ->preload(),
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'pending' => 'Pending',
                                'completed' => 'Selesai',
                                'canceled' => 'Dibatalkan',
                            ])
                            ->required()
                            ->default('completed'),
                    ])->columns(2),
            ]);
    }
}
