<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Laravel\Scout\Searchable;

class CustomerData extends Model
{
    use Searchable;

    protected $table = 'customer_data';

    /**
     * Get the indexable data array for the model.
     *
     * @return array<string, mixed>
     */
    public function toSearchableArray(): array
    {
        return [
            'id' => (int) $this->id,
            'nopol' => $this->nopol,
            'nama' => $this->nama,
            'nosin' => $this->nosin,
            'norak' => $this->norak,
            'alamat' => $this->alamat,
            'tipe' => $this->tipe,
            'is_active' => (bool) $this->is_active,
        ];
    }

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

    public function toSearchableArray(): array
    {
        return [
            'id' => (int) $this->id,
            'nopol' => $this->nopol,
            'nama' => $this->nama,
            'tipe' => $this->tipe,
            'norak' => $this->norak,
            'nosin' => $this->nosin,
            'is_active' => (int) $this->is_active,
        ];
    }
}
