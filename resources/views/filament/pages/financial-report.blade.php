<x-filament-panels::page>
    <div style="display: flex; flex-direction: column; gap: 24px; padding-bottom: 60px; font-family: Inter, ui-sans-serif, system-ui, -apple-system, sans-serif;">
        
        {{-- Section Filter --}}
        <div>
            {{ $this->form }}
        </div>

        {{-- Professional Results Section --}}
        @if(!empty($results))
            <div style="display: flex; flex-direction: column; gap: 16px;">
                
                {{-- Header with Action --}}
                <div style="display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #e5e7eb; padding: 0 12px 12px 12px;">
                    <div style="display: flex; flex-direction: column; gap: 2px;">
                        <h2 style="margin: 0; font-size: 20px; font-weight: 800; color: #111827; letter-spacing: -0.02em;">Pratinjau Laporan Keuangan</h2>
                        <p style="margin: 0; font-size: 12px; color: #6b7280;">Data transaksi terverifikasi sistem • Aliran kas PT</p>
                    </div>
                    
                    <x-filament::button 
                        wire:click="download" 
                        icon="heroicon-m-arrow-down-tray" 
                        color="success" 
                        style="padding: 8px 20px; border-radius: 8px; font-weight: 700;"
                    >
                        Download PDF
                    </x-filament::button>
                </div>

                {{-- Clean Data Table --}}
                <div style="background: white; border-radius: 12px; border: 1px solid #e5e7eb; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); overflow: hidden;">
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse; min-width: 1000px;">
                            <thead>
                                <tr style="background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                                    <th style="padding: 12px 20px; text-align: left; font-size: 10px; font-weight: 700; text-transform: uppercase; color: #6b7280; letter-spacing: 0.05em;">Tanggal</th>
                                    <th style="padding: 12px 20px; text-align: left; font-size: 10px; font-weight: 700; text-transform: uppercase; color: #6b7280; letter-spacing: 0.05em;">BASTK / Identitas</th>
                                    <th style="padding: 12px 20px; text-align: left; font-size: 10px; font-weight: 700; text-transform: uppercase; color: #6b7280; letter-spacing: 0.05em;">{{ $reportType === 'finance' ? 'Finance & Cabang' : 'Vendor' }}</th>
                                    <th style="padding: 12px 20px; text-align: center; font-size: 10px; font-weight: 700; text-transform: uppercase; color: #6b7280; width: 150px; letter-spacing: 0.05em;">Arus Kas</th>
                                    <th style="padding: 12px 20px; text-align: center; font-size: 10px; font-weight: 700; text-transform: uppercase; color: #6b7280; width: 100px; letter-spacing: 0.05em;">Status</th>
                                    <th style="padding: 12px 20px; text-align: right; font-size: 10px; font-weight: 700; text-transform: uppercase; color: #6b7280; width: 160px; letter-spacing: 0.05em;">Nominal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($results as $index => $item)
                                    <tr style="border-bottom: 1px solid #f3f4f6;">
                                        <td style="padding: 12px 20px; font-size: 13px; color: #374151;">
                                            {{ \Carbon\Carbon::parse($item->withdrawal_date)->format('d/m/Y') }}
                                        </td>
                                        <td style="padding: 12px 20px;">
                                            <div style="display: flex; flex-direction: column;">
                                                <span style="font-size: 13px; font-weight: 700; color: #111827;">{{ $item->bastk->number ?? 'N/A' }}</span>
                                                <span style="font-size: 11px; color: #9ca3af; font-weight: 500;">{{ $item->customerData->nopol ?? '-' }} • {{ $item->customerData->tipe ?? '-' }}</span>
                                            </div>
                                        </td>
                                        <td style="padding: 12px 20px;">
                                            @if($reportType === 'finance')
                                                <div style="display: flex; flex-direction: column;">
                                                    <span style="font-size: 13px; font-weight: 600; color: #374151;">{{ $item->customerData->financeBranch->financeMaster->fin_name ?? '-' }}</span>
                                                    <span style="font-size: 11px; color: #9ca3af; font-style: italic; font-weight: 500;">{{ $item->customerData->financeBranch->locationMaster->name ?? '-' }}</span>
                                                </div>
                                            @else
                                                <span style="font-size: 13px; font-weight: 600; color: #374151;">{{ $item->vendor->nama ?? '-' }}</span>
                                            @endif
                                        </td>
                                        <td style="padding: 12px 20px; text-align: center;">
                                            @if($reportType === 'finance')
                                                <div style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; border-radius: 20px; background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0;">
                                                    <svg style="width: 12px; height: 12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                                                    <span style="font-size: 10px; font-weight: 800; text-transform: uppercase;">Dana Masuk</span>
                                                </div>
                                            @else
                                                <div style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; border-radius: 20px; background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca;">
                                                    <svg style="width: 12px; height: 12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                                                    <span style="font-size: 10px; font-weight: 800; text-transform: uppercase;">Dana Keluar</span>
                                                </div>
                                            @endif
                                        </td>
                                        <td style="padding: 12px 20px; text-align: center;">
                                            @php
                                                $statusLabel = $reportType === 'finance' 
                                                    ? ($item->is_finance_paid ? ['bg'=>'#f0fdf4', 'cl'=>'#166534', 'bd'=>'#bbf7d0', 'lb'=>'CAIR'] : ['bg'=>'#fffbeb', 'cl'=>'#92400e', 'bd'=>'#fef3c7', 'lb'=>'PENDING'])
                                                    : match($item->status) {
                                                        'paid' => ['bg'=>'#f0fdf4', 'cl'=>'#166534', 'bd'=>'#bbf7d0', 'lb'=>'LUNAS'],
                                                        'validated' => ['bg'=>'#eff6ff', 'cl'=>'#1e40af', 'bd'=>'#dbeafe', 'lb'=>'VALIDATED'],
                                                        default => ['bg'=>'#fffbeb', 'cl'=>'#92400e', 'bd'=>'#fef3c7', 'lb'=>'PENDING'],
                                                    };
                                            @endphp
                                            <span style="display: inline-block; padding: 3px 10px; border-radius: 6px; background: {{ $statusLabel['bg'] }}; color: {{ $statusLabel['cl'] }}; font-size: 9px; font-weight: 700; border: 1px solid {{ $statusLabel['bd'] }};">{{ $statusLabel['lb'] }}</span>
                                        </td>
                                        <td style="padding: 12px 20px; text-align: right;">
                                            @php
                                                $nominal = $reportType === 'finance' 
                                                    ? ($item->estimated_payout ?? 0)
                                                    : (($item->bailout_amount ?? 0) + ($item->vendor_fee ?? 0));
                                            @endphp
                                            <span style="font-size: 14px; font-weight: 800; color: {{ $reportType === 'finance' ? '#15803d' : '#b91c1c' }}; font-family: 'JetBrains Mono', 'Fira Code', monospace;">
                                                {{ $reportType === 'finance' ? '+' : '-' }} Rp {{ number_format($nominal, 0, ',', '.') }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot style="background: #1e293b; color: white;">
                                <tr>
                                    <td colspan="5" style="padding: 12px 24px; text-align: right;">
                                        <div style="display: flex; flex-direction: row; align-items: center; justify-content: flex-end; gap: 12px;">
                                            <span style="font-size: 10px; font-weight: 600; text-transform: uppercase; color: #94a3b8; letter-spacing: 0.1em;">Ringkasan {{ $reportType === 'finance' ? 'Pendapatan' : 'Pengeluaran' }}</span>
                                            <span style="font-size: 12px; font-weight: 800; color: white;">TOTAL {{ $reportType === 'finance' ? 'DANA MASUK' : 'DANA KELUAR' }}</span>
                                        </div>
                                    </td>
                                    <td style="padding: 12px 24px; text-align: right; background: {{ $reportType === 'finance' ? '#15803d' : '#b91c1c' }};">
                                        <div style="display: flex; align-items: center; justify-content: flex-end; gap: 6px; font-family: 'JetBrains Mono', 'Fira Code', monospace;">
                                            <span style="font-size: 14px; font-weight: 800; opacity: 0.9;">{{ $reportType === 'finance' ? '+' : '-' }} Rp</span>
                                            <span style="font-size: 18px; font-weight: 900; letter-spacing: -0.5px;">
                                                {{ number_format($grandTotal, 0, ',', '.') }}
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <script>
        window.addEventListener('download-file', event => {
            const data = event.detail[0];
            const link = document.createElement('a');
            link.href = 'data:application/pdf;base64,' + data.content;
            link.download = data.filename;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });
    </script>
</x-filament-panels::page>