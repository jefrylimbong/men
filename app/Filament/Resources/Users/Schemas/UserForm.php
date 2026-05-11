<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Components\Grid;
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

                        ]),
                ])->columnSpan(1),

                Section::make('Hak Akses Menu')
                    ->description('Pilih menu dan aksi yang diizinkan untuk user ini')
                    ->visible(fn (Get $get) => filled($get('type')) && $get('type') !== 'user')
                    ->columnSpanFull()
                    ->schema([
                        // Header Row
                        Grid::make(5)
                            ->schema([
                                Placeholder::make('menu_label')
                                    ->label('Nama Menu')
                                    ->content(''),
                                Action::make('all_view')
                                    ->label('View All')
                                    ->icon('heroicon-m-eye')
                                    ->color('success')
                                    ->button()
                                    ->action(fn (Set $set, Get $get) => static::toggleGlobal($set, $get, 'view')),
                                Action::make('all_add')
                                    ->label('Add All')
                                    ->icon('heroicon-m-plus-circle')
                                    ->color('info')
                                    ->button()
                                    ->action(fn (Set $set, Get $get) => static::toggleGlobal($set, $get, 'create')),
                                Action::make('all_edit')
                                    ->label('Edit All')
                                    ->icon('heroicon-m-pencil-square')
                                    ->color('warning')
                                    ->button()
                                    ->action(fn (Set $set, Get $get) => static::toggleGlobal($set, $get, 'update')),
                                Action::make('all_delete')
                                    ->label('Del All')
                                    ->icon('heroicon-m-trash')
                                    ->color('danger')
                                    ->button()
                                    ->action(fn (Set $set, Get $get) => static::toggleGlobal($set, $get, 'delete')),
                            ]),

                        // Resource Rows
                        SchemaGroup::make([
                            ...collect(static::$resources)->map(function ($label, $resource) {
                                return Grid::make(5)
                                    ->visible(function (Get $get) use ($resource) {
                                        if ($resource === 'AppErrors') {
                                            return auth()->user()?->type === 'superadmin' && $get('type') === 'superadmin';
                                        }

                                        return true;
                                    })
                                    ->schema([
                                        Placeholder::make("label_$resource")
                                            ->label('')
                                            ->content($label),
                                        static::getPermissionToggle($resource, 'view'),
                                        static::getPermissionToggle($resource, 'create'),
                                        static::getPermissionToggle($resource, 'update'),
                                        static::getPermissionToggle($resource, 'delete'),
                                    ]);
                            })->toArray(),
                        ]),

                        Action::make('reset_all')
                            ->label('Reset Semua Izin')
                            ->icon('heroicon-m-x-circle')
                            ->color('gray')
                            ->link()
                            ->action(function (Set $set) {
                                foreach (static::$resources as $resource => $label) {
                                    $set("permissions.$resource", []);
                                }
                            }),
                    ]),
            ])->columns(2);
    }

    protected static function toggleGlobal(Set $set, Get $get, string $type): void
    {
        $current = $get('permissions') ?? [];
        $allChecked = true;
        foreach (static::$resources as $resource => $label) {
            $prefix = $type === 'create' ? 'create' : ($type === 'update' ? 'update' : $type);
            if (! in_array("{$prefix}_{$resource}", $current[$resource] ?? [])) {
                $allChecked = false;
                break;
            }
        }

        foreach (static::$resources as $resource => $label) {
            $prefix = $type === 'create' ? 'create' : ($type === 'update' ? 'update' : $type);
            $resPerms = $current[$resource] ?? [];
            if ($allChecked) {
                $set("permissions.$resource", array_diff($resPerms, ["{$prefix}_{$resource}"]));
            } else {
                $set("permissions.$resource", array_unique(array_merge($resPerms, ["{$prefix}_{$resource}"])));
            }
        }
    }

    protected static function getPermissionToggle(string $resource, string $type): Checkbox
    {
        $prefix = $type === 'create' ? 'create' : ($type === 'update' ? 'update' : $type);
        $permissionString = "{$prefix}_{$resource}";

        return Checkbox::make("checkbox_{$resource}_{$type}")
            ->label('')
            ->formatStateUsing(fn (Get $get) => in_array($permissionString, $get("permissions.$resource") ?? []))
            ->live()
            ->afterStateUpdated(function ($state, Set $set, Get $get) use ($resource, $permissionString) {
                $current = $get("permissions.$resource") ?? [];
                if ($state) {
                    $set("permissions.$resource", array_unique(array_merge($current, [$permissionString])));
                } else {
                    $set("permissions.$resource", array_diff($current, [$permissionString]));
                }
            });
    }
}
