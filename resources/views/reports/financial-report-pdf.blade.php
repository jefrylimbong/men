<!DOCTYPE html>
<html>
<head>
    <title>Laporan Keuangan</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .header { text-align: center; margin-bottom: 30px; }
        .footer { margin-top: 30px; text-align: right; }
        .total-row { font-weight: bold; background-color: #f9f9f9; }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN DETAIL {{ strtoupper($type) }}</h2>
        <p>Periode: {{ $date_start }} s/d {{ $date_end }}</p>
        @if($entity_name)
            <p>Target: {{ $entity_name }}</p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Nopol / Kendaraan</th>
                @if($type == 'finance')
                    <th>Finance</th>
                    <th>Estimasi Cair</th>
                @else
                    <th>Vendor</th>
                    <th>Total Bayar ke Vendor</th>
                @endif
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @php $total = 0; @endphp
            @foreach($data as $index => $item)
                @php 
                    $rowTotal = ($type == 'finance') 
                        ? $item->estimated_payout 
                        : ($item->bailout_amount + $item->vendor_fee);
                    $total += $rowTotal;
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->withdrawal_date }}</td>
                    <td>{{ $item->customerData->nopol }} - {{ $item->customerData->tipe }}</td>
                    @if($type == 'finance')
                        <td>{{ $item->customerData->financeBranch->financeMaster->fin_name }}</td>
                        <td>Rp {{ number_format($item->estimated_payout, 0, ',', '.') }}</td>
                    @else
                        <td>{{ $item->vendor->nama ?? '-' }}</td>
                        <td>Rp {{ number_format($rowTotal, 0, ',', '.') }}</td>
                    @endif
                    <td>{{ ucfirst($item->status) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="{{ $type == 'finance' ? 4 : 3 }}" style="text-align: right;">GRAND TOTAL:</td>
                <td>Rp {{ number_format($total, 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>Dicetak pada: {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</body>
</html>
