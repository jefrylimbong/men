<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CustomerDataTemplateExport implements FromArray, WithHeadings
{
    public function array(): array
    {
        return [
            [
                'Contoh Nama',
                'B 1234 ABC',
                'MHF123...',
                'NOSIN123...',
                'Vario 150',
                '36',
                '12',
                '0',
                '0',
                'Jl. Raya No. 1',
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'nama',
            'nopol',
            'no_rangka',
            'no_mesin',
            'tipe',
            'tenor',
            'ke',
            'od',
            'ph',
            'alamat',
        ];
    }
}
