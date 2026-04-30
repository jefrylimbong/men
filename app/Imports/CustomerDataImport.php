<?php

namespace App\Imports;

use App\Models\CustomerData;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class CustomerDataImport implements ToModel, WithHeadingRow, WithValidation
{
    public function __construct(protected int $branchId) {}

    /**
     * @return Model|null
     */
    public function model(array $row)
    {
        return new CustomerData([
            'nama' => $row['nama'],
            'nopol' => $row['nopol'],
            'norak' => $row['no_rangka'] ?? $row['norak'] ?? null,
            'nosin' => $row['no_mesin'] ?? $row['nosin'] ?? null,
            'tipe' => $row['tipe'] ?? null,
            'tenor' => $row['tenor'] ?? null,
            'ke' => $row['ke'] ?? null,
            'od' => $row['od'] ?? null,
            'ph' => $row['ph'] ?? null,
            'alamat' => $row['alamat'] ?? null,
            'finance_branch_id' => $this->branchId,
            'is_active' => true,
        ]);
    }

    public function rules(): array
    {
        return [
            'nama' => 'required',
            'nopol' => 'required',
        ];
    }
}
