<?php

namespace App\Filament\Resources\CustomerData\Pages;

use App\Exports\CustomerDataTemplateExport;
use App\Filament\Resources\CustomerData\CustomerDataResource;
use App\Imports\CustomerDataImport;
use App\Models\FinanceBranch;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ListCustomerData extends ListRecords
{
    protected static string $resource = CustomerDataResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('downloadTemplate')
                ->label('Download Template')
                ->icon('heroicon-o-document-arrow-down')
                ->color('gray')
                ->action(fn () => Excel::download(new CustomerDataTemplateExport, 'template_customer_data.xlsx')),
            Action::make('import')
                ->label('Import Excel')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('success')
                ->form([
                    Select::make('finance_branch_id')
                        ->label('Pilih Cabang Target')
                        ->options(function () {
                            return FinanceBranch::query()
                                ->where('is_active', true)
                                ->join('finance_masters', 'finance_branches.finance_master_id', '=', 'finance_masters.id')
                                ->join('location_masters', 'finance_branches.location_master_id', '=', 'location_masters.id')
                                ->select('finance_branches.*', 'finance_masters.fin_name as finance_name', 'location_masters.name as location_name')
                                ->orderBy('finance_masters.fin_name')
                                ->orderBy('location_masters.name')
                                ->get()
                                ->groupBy('finance_name')
                                ->map(function ($branches) {
                                    return $branches->mapWithKeys(function ($branch) {
                                        return [$branch->id => "    {$branch->location_name}"]; // Using non-breaking spaces for reliable indentation
                                    });
                                })
                                ->toArray();
                        })
                        ->searchable()
                        ->required(),
                    FileUpload::make('file')
                        ->label('Pilih File Excel')
                        ->helperText('Gunakan template yang sudah disediakan. Data akan masuk ke cabang yang dipilih di atas.')
                        ->disk('public')
                        ->directory('imports')
                        ->required(),
                ])
                ->action(function (array $data) {
                    $file = Storage::disk('public')->path($data['file']);

                    try {
                        Excel::import(new CustomerDataImport($data['finance_branch_id']), $file);

                        Notification::make()
                            ->title('Data Berhasil Diimport')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Import Gagal')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
            CreateAction::make(),
        ];
    }
}
