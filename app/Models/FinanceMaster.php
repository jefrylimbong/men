<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class FinanceMaster extends Model
{
    protected $fillable = [
        'code_fin',
        'fin_name',
        'photo',
    ];

    public function branches(): HasMany
    {
        return $this->hasMany(FinanceBranch::class);
    }

    public function customers(): HasManyThrough
    {
        return $this->hasManyThrough(CustomerData::class, FinanceBranch::class);
    }
}
