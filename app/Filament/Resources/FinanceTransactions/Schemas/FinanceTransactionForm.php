<?php

namespace App\Filament\Resources\FinanceTransactions\Schemas;

use App\Models\FinanceMaster;
use App\Models\Vendor;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FinanceTransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Utama & Status')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                DatePicker::make('transaction_date')
                                    ->label('Tanggal Transaksi')
                                    ->required()
                                    ->default(now())
                                    ->disabled(fn ($record) => $record && $record->reference_id),
                                Select::make('category')
                                    ->label('Kategori')
                                    ->options([
                                        'penarikan' => 'Biaya Penarikan',
                                        'operational' => 'Operasional Kantor',
                                        'billing' => 'Penagihan Finance',
                                        'payment' => 'Pembayaran Vendor',
                                        'other' => 'Lainnya',
                                    ])
                                    ->required()
                                    ->disabled(fn ($record) => $record && $record->reference_id),
                                TextInput::make('amount')
                                    ->label('Jumlah (Nominal)')
                                    ->prefix('Rp')
                                    ->extraAttributes(['x-mask' => '9.999.999.999.999'])
                                    ->afterStateHydrated(function ($state, $set) {
                                        if ($state) {
                                            $set('amount', number_format((float) $state, 0, ',', '.'));
                                        }
                                    })
                                    ->dehydrateStateUsing(fn ($state) => $state ? str_replace('.', '', $state) : 0)
                                    ->required()
                                    ->reactive()
                                    ->disabled(fn ($record) => $record && $record->reference_id),
                            ]),
                        Grid::make(3)
                            ->schema([
                                TextInput::make('description')
                                    ->label('Keterangan / Memo')
                                    ->placeholder('Tulis catatan tambahan di sini...')
                                    ->columnSpan(2),
                                Select::make('status')
                                    ->label('Status')
                                    ->options([
                                        'pending' => 'Pending',
                                        'completed' => 'Selesai',
                                        'canceled' => 'Dibatalkan',
                                    ])
                                    ->required()
                                    ->default('completed')
                                    ->columnSpan(1),
                            ]),
                        Select::make('reference_id')
                            ->label('Terhubung ke Data Penarikan')
                            ->relationship('reference', 'id')
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->customerData->nopol} - ".($record->bastk->number ?? 'Tanpa BASTK'))
                            ->searchable()
                            ->preload()
                            ->placeholder('Pilih data penarikan jika ada...')
                            ->helperText(fn ($record) => $record && $record->reference_id ? 'Transaksi ini dikunci karena terhubung secara otomatis dengan data penarikan di atas.' : 'Gunakan untuk menelusuri transaksi ini kembali ke unit kendaraan tertentu')
                            ->disabled(fn ($record) => $record && $record->reference_id),
                    ])
                    ->columnSpanFull(),

                Section::make('Aliran Arus Kas (Penerima & Pengirim)')
                    ->description('Pastikan arah dana sudah benar untuk laporan Cash Flow yang akurat')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Grid::make(1)
                                    ->schema([
                                        Select::make('debit_type')
                                            ->label('DANA MASUK KE (Penerima)')
                                            ->options([
                                                'PT' => 'PT (Internal)',
                                                'Vendor' => 'Vendor',
                                                'Finance' => 'Perusahaan Finance',
                                            ])
                                            ->reactive()
                                            ->required()
                                            ->prefixIcon('heroicon-m-arrow-down-circle')
                                            ->prefixIconColor('success')
                                            ->disabled(fn ($record) => $record && $record->reference_id),
                                        Select::make('debit_id')
                                            ->label('Detail Entitas Penerima')
                                            ->options(function (callable $get) {
                                                $type = $get('debit_type');
                                                if ($type === 'Vendor') {
                                                    return Vendor::pluck('nama', 'id');
                                                }
                                                if ($type === 'Finance') {
                                                    return FinanceMaster::pluck('fin_name', 'id');
                                                }

                                                return ['0' => 'Kas PT Internal'];
                                            })
                                            ->searchable()
                                            ->hidden(fn (callable $get) => ! $get('debit_type'))
                                            ->required()
                                            ->disabled(fn ($record) => $record && $record->reference_id),
                                    ])
                                    ->columnSpan(1),

                                Grid::make(1)
                                    ->schema([
                                        Select::make('credit_type')
                                            ->label('DANA KELUAR DARI (Pengirim)')
                                            ->options([
                                                'PT' => 'PT (Internal)',
                                                'Vendor' => 'Vendor',
                                                'Finance' => 'Perusahaan Finance',
                                            ])
                                            ->reactive()
                                            ->required()
                                            ->prefixIcon('heroicon-m-arrow-up-circle')
                                            ->prefixIconColor('danger')
                                            ->disabled(fn ($record) => $record && $record->reference_id),
                                        Select::make('credit_id')
                                            ->label('Detail Entitas Pengirim')
                                            ->options(function (callable $get) {
                                                $type = $get('credit_type');
                                                if ($type === 'Vendor') {
                                                    return Vendor::pluck('nama', 'id');
                                                }
                                                if ($type === 'Finance') {
                                                    return FinanceMaster::pluck('fin_name', 'id');
                                                }

                                                return ['0' => 'Kas PT Internal'];
                                            })
                                            ->searchable()
                                            ->hidden(fn (callable $get) => ! $get('credit_type'))
                                            ->required()
                                            ->disabled(fn ($record) => $record && $record->reference_id),
                                    ])
                                    ->columnSpan(1),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
