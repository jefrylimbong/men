<?php

namespace App\Filament\Pages;

use App\Models\FinanceMaster;
use App\Models\Vendor;
use App\Models\WithdrawalData;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class FinancialReport extends Page
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static string|\UnitEnum|null $navigationGroup = 'Keuangan';

    protected static ?string $navigationLabel = 'Laporan Keuangan';

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.financial-report';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'date_start' => now()->startOfMonth()->format('Y-m-d'),
            'date_end' => now()->endOfMonth()->format('Y-m-d'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Filter Laporan')
                    ->schema([
                        Select::make('type')
                            ->label('Jenis Laporan')
                            ->options([
                                'finance' => 'Tagihan ke Finance',
                                'vendor' => 'Pembayaran ke Vendor',
                            ])
                            ->required()
                            ->reactive(),

                        Select::make('vendor_id')
                            ->label('Pilih Vendor')
                            ->options(Vendor::pluck('nama', 'id'))
                            ->searchable()
                            ->visible(fn ($get) => $get('type') === 'vendor'),

                        Select::make('finance_master_id')
                            ->label('Pilih Finance')
                            ->options(FinanceMaster::pluck('fin_name', 'id'))
                            ->searchable()
                            ->visible(fn ($get) => $get('type') === 'finance'),

                        DatePicker::make('date_start')
                            ->label('Dari Tanggal')
                            ->required(),

                        DatePicker::make('date_end')
                            ->label('Sampai Tanggal')
                            ->required(),
                    ])->columns(2),
            ])
            ->statePath('data');
    }

    public function download(): void
    {
        $data = $this->form->getState();

        $query = WithdrawalData::query()
            ->whereBetween('withdrawal_date', [$data['date_start'], $data['date_end']])
            ->with(['customerData.financeBranch.financeMaster', 'vendor']);

        $entityName = 'Semua';

        if ($data['type'] === 'vendor' && $data['vendor_id']) {
            $query->where('vendor_id', $data['vendor_id']);
            $entityName = Vendor::find($data['vendor_id'])?->nama;
        }

        if ($data['type'] === 'finance' && $data['finance_master_id']) {
            $query->whereHas('customerData.financeBranch', function ($q) use ($data) {
                $q->where('finance_master_id', $data['finance_master_id']);
            });
            $entityName = FinanceMaster::find($data['finance_master_id'])?->fin_name;
        }

        $results = $query->get();

        if ($results->isEmpty()) {
            Notification::make()
                ->title('Tidak ada data')
                ->body('Tidak ditemukan data penarikan untuk periode ini.')
                ->warning()
                ->send();

            return;
        }

        $pdf = Pdf::loadView('reports.financial-report-pdf', [
            'type' => $data['type'],
            'data' => $results,
            'date_start' => $data['date_start'],
            'date_end' => $data['date_end'],
            'entity_name' => $entityName,
        ]);

        $filename = 'Laporan_'.ucfirst($data['type']).'_'.now()->format('Ymd_His').'.pdf';

        // Filament way to download
        $this->dispatch('download-file', [
            'content' => base64_encode($pdf->output()),
            'filename' => $filename,
        ]);
    }
}
