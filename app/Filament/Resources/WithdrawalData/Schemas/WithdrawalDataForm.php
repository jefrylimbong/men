<?php

namespace App\Filament\Resources\WithdrawalData\Schemas;

use App\Models\BastkRegister;
use App\Models\CustomerData;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class WithdrawalDataForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Kendaraan (Master)')
                    ->schema([
                        Select::make('customer_data_id')
                            ->label('Cari Data Kendaraan (Nopol)')
                            ->relationship('customerData', 'nopol')
                            ->searchable()
                            ->preload()
                            ->reactive()
                            ->required(),

                        Placeholder::make('customer_info')
                            ->label('Detail Kendaraan')
                            ->content(function ($get) {
                                $id = $get('customer_data_id');
                                if (! $id) {
                                    return 'Silakan pilih kendaraan';
                                }
                                $data = CustomerData::find($id);
                                if (! $data) {
                                    return 'Data tidak ditemukan';
                                }

                                return new HtmlString("
                                    <div class='grid grid-cols-2 gap-2 text-sm'>
                                        <div><b>Nama:</b> {$data->nama}</div>
                                        <div><b>No Plat:</b> {$data->nopol}</div>
                                        <div><b>Tipe:</b> {$data->tipe}</div>
                                        <div><b>Finance:</b> ".($data->financeBranch->financeMaster->fin_name ?? '-').'</div>
                                        <div><b>Cabang:</b> '.($data->financeBranch->locationMaster->name ?? '-')."</div>
                                        <div><b>No Mesin:</b> {$data->nosin}</div>
                                        <div><b>No Rangka:</b> {$data->norak}</div>
                                    </div>
                                ");
                            })
                            ->columnSpanFull(),
                    ]),

                Section::make('Informasi Penarikan & Lapangan')
                    ->schema([
                        DatePicker::make('withdrawal_date')
                            ->label('Tanggal Penarikan')
                            ->required()
                            ->default(now()),
                        Select::make('user_id')
                            ->label('Orang Lapangan')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('vendor_id')
                            ->label('Vendor Pendana')
                            ->relationship('vendor', 'nama')
                            ->searchable()
                            ->preload(),
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'pending' => 'Pending',
                                'validated' => 'Tervalidasi',
                                'paid' => 'Lunas',
                                'canceled' => 'Dibatalkan',
                            ])
                            ->default('pending')
                            ->required(),
                    ])->columns(2),

                Section::make('BASTK (Berita Acara)')
                    ->schema([
                        Toggle::make('is_bastk_ready')
                            ->label('BASTK Tersedia?')
                            ->reactive()
                            ->dehydrated(false)
                            ->afterStateHydrated(function ($set, $record) {
                                $set('is_bastk_ready', $record && $record->bastk()->exists());
                            }),
                        Grid::make(2)
                            ->schema([
                                Select::make('bastk_id')
                                    ->label('Pilih Nomor BASTK')
                                    ->options(function ($record) {
                                        $query = BastkRegister::query()
                                            ->where('status', true);

                                        if ($record && $record->bastk) {
                                            $query->where(function ($q) use ($record) {
                                                $q->whereNull('withdrawal_data_id')
                                                    ->orWhere('id', $record->bastk->id);
                                            });
                                        } else {
                                            $query->whereNull('withdrawal_data_id');
                                        }

                                        return $query->pluck('number', 'id');
                                    })
                                    ->getOptionLabelUsing(fn ($value): ?string => BastkRegister::find($value)?->number)
                                    ->searchable()
                                    ->preload()
                                    ->dehydrated(false)
                                    ->required(fn ($get) => $get('is_bastk_ready'))
                                    ->visible(fn ($get) => $get('is_bastk_ready'))
                                    ->afterStateHydrated(function ($set, $record) {
                                        if ($record && $record->bastk) {
                                            $set('bastk_id', $record->bastk->id);
                                        }
                                    }),
                                Toggle::make('bastk_status')
                                    ->label('BASTK Valid?')
                                    ->visible(fn ($get) => $get('is_bastk_ready'))
                                    ->dehydrated(false)
                                    ->default(true)
                                    ->afterStateHydrated(function ($set, $record) {
                                        if ($record && $record->bastk) {
                                            $set('bastk_status', $record->bastk->status);
                                        }
                                    }),
                                FileUpload::make('bastk_photos')
                                    ->label('Foto Dokumentasi BASTK')
                                    ->multiple()
                                    ->image()
                                    ->dehydrated(false)
                                    ->visible(fn ($get) => $get('is_bastk_ready'))
                                    ->directory('bastk-photos')
                                    ->columnSpanFull()
                                    ->afterStateHydrated(function ($set, $record) {
                                        if ($record && $record->bastk) {
                                            $set('bastk_photos', $record->bastk->photos);
                                        }
                                    }),
                                FileUpload::make('bastk_files')
                                    ->label('File Lampiran BASTK')
                                    ->multiple()
                                    ->dehydrated(false)
                                    ->visible(fn ($get) => $get('is_bastk_ready'))
                                    ->directory('bastk-files')
                                    ->columnSpanFull()
                                    ->afterStateHydrated(function ($set, $record) {
                                        if ($record && $record->bastk) {
                                            $set('bastk_files', $record->bastk->files);
                                        }
                                    }),
                            ]),
                    ]),

                Section::make('Rincian Keuangan')
                    ->schema([
                        TextInput::make('estimated_payout')
                            ->label('Estimasi Cair Finance (Tagihan)')
                            ->prefix('Rp')
                            ->extraAttributes(['x-mask' => '9.999.999.999.999'])
                            ->afterStateHydrated(function ($state, $set) {
                                if ($state) {
                                    $set('estimated_payout', number_format((float) $state, 0, ',', '.'));
                                }
                            })
                            ->dehydrateStateUsing(fn ($state) => $state ? str_replace('.', '', $state) : 0)
                            ->reactive()
                            ->required()
                            ->columnSpan(1),
                        TextInput::make('handling_fee')
                            ->label('Biaya Penanganan')
                            ->numeric()
                            ->prefix('Rp')
                            ->reactive()
                            ->columnSpan(1),
                        TextInput::make('bailout_amount')
                            ->label('Dana Talangan Vendor')
                            ->numeric()
                            ->prefix('Rp')
                            ->reactive()
                            ->afterStateUpdated(function ($state, $set) {
                                $set('vendor_fee', $state ? $state * 0.1 : 0);
                            })
                            ->columnSpan(1),
                        TextInput::make('vendor_fee')
                            ->label('Fee Vendor (10%)')
                            ->numeric()
                            ->prefix('Rp')
                            ->reactive()
                            ->helperText('Default 10%, bisa diubah manual')
                            ->columnSpan(1),

                        Placeholder::make('summary')
                            ->label('Ringkasan Estimasi Profit')
                            ->content(function ($get) {
                                $estCair = (float) $get('estimated_payout') ?: 0;
                                $talangan = (float) $get('bailout_amount') ?: 0;
                                $feeVendor = (float) $get('vendor_fee') ?: 0;
                                $handling = (float) $get('handling_fee') ?: 0;

                                $totalVendor = $talangan + $feeVendor;
                                $profitBersih = $estCair - $totalVendor - $handling;

                                return new HtmlString("
                                    <div class='space-y-1 text-sm'>
                                        <div class='flex justify-between'>
                                            <span>Total Bayar ke Vendor:</span>
                                            <span class='font-medium'>Rp ".number_format($totalVendor, 0, ',', '.')."</span>
                                        </div>
                                        <div class='flex justify-between text-success-600 font-bold border-t pt-1'>
                                            <span>Estimasi Keuntungan PT:</span>
                                            <span>Rp ".number_format($profitBersih, 0, ',', '.').'</span>
                                        </div>
                                    </div>
                                ');
                            })
                            ->columnSpanFull(),

                        Toggle::make('is_finance_paid')
                            ->label('Dana Finance Cair?')
                            ->reactive()
                            ->columnSpanFull(),
                        TextInput::make('finance_payout')
                            ->label('Nominal Cair Finance (Aktual)')
                            ->numeric()
                            ->prefix('Rp')
                            ->visible(fn ($get) => $get('is_finance_paid'))
                            ->columnSpan(1),
                        DatePicker::make('finance_deadline')
                            ->label('Deadline Pembayaran Finance')
                            ->visible(fn ($get) => ! $get('is_finance_paid'))
                            ->required(fn ($get) => ! $get('is_finance_paid'))
                            ->helperText('Trigger tanggal jatuh tempo untuk finance')
                            ->columnSpan(1),
                    ])->columns(2),
            ]);
    }
}
