<?php

namespace App\Filament\Pages;

use App\Models\FinanceMaster;
use App\Models\Vendor;
use App\Models\WithdrawalData;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FinancialReport extends Page
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static string|\UnitEnum|null $navigationGroup = 'Keuangan';

    protected static ?string $navigationLabel = 'Laporan Keuangan';

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.financial-report';

    public ?array $data = [];

    public $results = [];

    public $reportType = '';

    public $grandTotal = 0;

    public function mount(): void
    {
        $this->form->fill([
            'date_start' => now()->startOfMonth()->format('Y-m-d'),
            'date_end' => now()->endOfMonth()->format('Y-m-d'),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Filter Laporan')
                    ->schema([
                        Select::make('type')
                            ->label('Jenis Laporan')
                            ->options([
                                'finance' => 'Tagihan ke Finance',
                                'vendor' => 'Pembayaran ke Vendor',
                            ])
                            ->required()
                            ->live(),

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
                    ])
                    ->columns(2)
                    ->footerActions([
                        Action::make('search')
                            ->label('Tampilkan Laporan')
                            ->icon('heroicon-o-magnifying-glass')
                            ->color('info')
                            ->action('search'),
                    ]),
            ])
            ->statePath('data');
    }

    public function search(): void
    {
        $formData = $this->form->getState();
        $this->reportType = $formData['type'];

        $query = WithdrawalData::query()
            ->whereBetween('withdrawal_date', [$formData['date_start'], $formData['date_end']])
            ->with(['customerData.financeBranch.financeMaster', 'vendor', 'bastk'])
            ->orderBy('withdrawal_date', 'desc');

        if ($formData['type'] === 'vendor' && $formData['vendor_id']) {
            $query->where('vendor_id', $formData['vendor_id']);
        }

        if ($formData['type'] === 'finance' && $formData['finance_master_id']) {
            $query->whereHas('customerData.financeBranch', function ($q) use ($formData) {
                $q->where('finance_master_id', $formData['finance_master_id']);
            });
        }

        $this->results = $query->get();

        $this->grandTotal = $this->results->sum(function ($item) {
            return $this->reportType === 'finance'
                ? ($item->estimated_payout ?? 0)
                : (($item->bailout_amount ?? 0) + ($item->vendor_fee ?? 0));
        });

        if ($this->results->isEmpty()) {
            Notification::make()
                ->title('Tidak ada data')
                ->warning()
                ->send();
        }
    }

    public function download(): void
    {
        $formData = $this->form->getState();

        $query = WithdrawalData::query()
            ->whereBetween('withdrawal_date', [$formData['date_start'], $formData['date_end']])
            ->with(['customerData.financeBranch.financeMaster', 'vendor', 'bastk'])
            ->orderBy('withdrawal_date', 'desc');

        $entityName = 'Semua';

        if ($formData['type'] === 'vendor' && $formData['vendor_id']) {
            $query->where('vendor_id', $formData['vendor_id']);
            $entityName = Vendor::find($formData['vendor_id'])?->nama;
        }

        if ($formData['type'] === 'finance' && $formData['finance_master_id']) {
            $query->whereHas('customerData.financeBranch', function ($q) use ($formData) {
                $q->where('finance_master_id', $formData['finance_master_id']);
            });
            $entityName = FinanceMaster::find($formData['finance_master_id'])?->fin_name;
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
            'type' => $formData['type'],
            'data' => $results,
            'date_start' => $formData['date_start'],
            'date_end' => $formData['date_end'],
            'entity_name' => $entityName,
        ]);

        $filename = 'Laporan_'.ucfirst($formData['type']).'_'.now()->format('Ymd_His').'.pdf';

        $this->dispatch('download-file', [
            'content' => base64_encode($pdf->output()),
            'filename' => $filename,
        ]);
    }
}
