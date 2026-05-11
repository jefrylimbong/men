<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Components\Group as SchemaGroup;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class UserForm
{
    protected static array $resources = [
        'Users' => 'Manajemen User',
        'CustomerData' => 'Data Customer',
        'WithdrawalData' => 'Data Penarikan',
        'BastkRegisters' => 'Bastk Register',
        'LocationMasters' => 'Master Lokasi',
        'FinanceMasters' => 'Master Finance',
        'FinanceBranches' => 'Cabang Finance',
        'FinanceSearches' => 'Pencarian Finance',
        'FinanceTransactions' => 'Transaksi Kas',
        'Vendors' => 'Vendor / Jasa Penarikan',
        'AndroidActionHistories' => 'Histori Aksi Android',
        'AppErrors' => 'Laporan Error (Crashes)',
    ];

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                SchemaGroup::make([
                    Section::make('Informasi Login')
                        ->schema([
                            TextInput::make('name')
                                ->required(),
                            TextInput::make('email')
                                ->email()
                                ->required()
                                ->unique(ignoreRecord: true),
                            TextInput::make('username')
                                ->unique(ignoreRecord: true),
                            TextInput::make('password')
                                ->password()
                                ->dehydrated(fn ($state) => filled($state))
                                ->required(fn ($livewire) => $livewire instanceof CreateRecord)
                                ->visibleOn('create'),
                            Toggle::make('is_active')
                                ->label('Aktif')
                                ->default(true),
                        ])->columns(2),

                    Section::make('Profil Pengguna')
                        ->schema([
                            FileUpload::make('avatar')
                                ->image()
                                ->disk('public')
                                ->directory('avatars')
                                ->default('avatars/user.png'),
                            TextInput::make('phone')
                                ->tel(),
                            TextInput::make('nik')
                                ->label('NIK'),
                            DatePicker::make('date_birth')
                                ->label('Tanggal Lahir'),
                            TextInput::make('place_birth')
                                ->label('Tempat Lahir'),
                            Textarea::make('address')
                                ->label('Alamat')
                                ->columnSpanFull(),
                        ])->columns(2),
                ])->columnSpan(1),

                SchemaGroup::make([
                    Section::make('Akses & Izin')
                        ->schema([
                            DatePicker::make('access_expired')
                                ->label('Akses Berakhir'),
                            TextInput::make('data_limit')
                                ->numeric()
                                ->label('Total Kuota Data')
                                ->helperText('Berapa banyak data yang bisa diakses user di Flutter'),
                            Select::make('type')
                                ->label('Tipe User')
                                ->options(function () {
                                    $options = [
                                        'admin' => 'Admin',
                                        'operator' => 'Operator',
                                        'user' => 'User',
                                    ];

                                    if (auth()->user()?->type === 'superadmin') {
                                        return ['superadmin' => 'Super Admin'] + $options;
                                    }

                                    return $options;
                                })
                                ->required()
                                ->live(),

                            Section::make('Akses Finance Master')
                                ->description('Pilih Finance yang boleh diakses user ini')
                                ->visible(fn (Get $get) => $get('type') === 'user')
                                ->schema([
                                    CheckboxList::make('financeMasters')
                                        ->label('')
                                        ->relationship('financeMasters', 'fin_name')
                                        ->bulkToggleable()
                                        ->columns(2),
                                ]),

                            Section::make('Hak Akses Menu')
                                ->description('Pilih menu dan aksi yang diizinkan untuk user ini')
                                ->visible(fn (Get $get) => filled($get('type')) && $get('type') !== 'user')
                                ->headerActions([
                                    Action::make('select_all_view')
                                        ->label('Pilih Semua View')
                                        ->icon('heroicon-m-check-circle')
                                        ->color('success')
                                        ->action(function (Set $set) {
                                            foreach (static::$resources as $resource => $label) {
                                                $set("permissions.$resource", ["view_$resource"]);
                                            }
                                        }),
                                    Action::make('deselect_all')
                                        ->label('Hapus Semua')
                                        ->icon('heroicon-m-x-circle')
                                        ->color('danger')
                                        ->action(function (Set $set) {
                                            foreach (static::$resources as $resource => $label) {
                                                $set("permissions.$resource", []);
                                            }
                                        }),
                                ])
                                ->schema(function (Get $get) {
                                    $checkboxes = [];
                                    foreach (static::$resources as $resource => $label) {
                                        $checkboxes[] = static::getPermissionCheckbox($resource, $label, $get);
                                    }

                                    return $checkboxes;
                                }),
                        ]),
                ])->columnSpan(1),
            ])->columns(2);
    }

    protected static function getPermissionCheckbox(string $resource, string $label, Get $get): CheckboxList
    {
        return CheckboxList::make("permissions.$resource")
            ->label($label)
            ->visible(function () use ($resource, $get) {
                if ($resource === 'AppErrors') {
                    return auth()->user()?->type === 'superadmin' && $get('type') === 'superadmin';
                }

                return true;
            })
            ->options([
                "view_$resource" => 'View',
                "create_$resource" => 'Add',
                "update_$resource" => 'Edit',
                "delete_$resource" => 'Delete',
            ])
            ->bulkToggleable()
            ->columns(4)
            ->gridDirection('row');
    }
}
