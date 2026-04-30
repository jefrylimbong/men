<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FinanceBranch extends Model
{
    protected $fillable = [
        'finance_master_id',
        'location_master_id',
        'is_active',
    ];

    public function financeMaster(): BelongsTo
    {
        return $this->belongsTo(FinanceMaster::class);
    }

    public function locationMaster(): BelongsTo
    {
        return $this->belongsTo(LocationMaster::class);
    }

    public function customers(): HasMany
    {
        return $this->hasMany(CustomerData::class);
    }
}
