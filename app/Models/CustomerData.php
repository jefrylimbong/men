<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Scout\Searchable;

class CustomerData extends Model
{
    use Searchable;

    protected $table = 'customer_data';

    protected $fillable = [
        'nopol',
        'norak',
        'nosin',
        'tipe',
        'nama',
        'tenor',
        'ke',
        'od',
        'ph',
        'finance_branch_id',
        'alamat',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the finance branch associated with the customer data.
     */
    public function financeBranch(): BelongsTo
    {
        return $this->belongsTo(FinanceBranch::class);
    }

    /**
     * Konfigurasi data yang dikirim ke Meilisearch
     */
    public function toSearchableArray(): array
    {
        // Deteksi apakah Nopol ini "asli" atau cuma Nomor Mesin (biasanya mesin ada tanda '-')
        $isRealNopol = (!str_contains($this->nopol, '-')) && (strlen($this->nopol) < 10);

        return [
            'id' => (int) $this->id,
            'nopol' => $this->nopol,
            'nama' => $this->nama,
            'tipe' => $this->tipe,
            'is_active' => (int) $this->is_active,
            'is_real_nopol' => $isRealNopol ? 1 : 0, // Kolom pembantu
        ];
    }
}
