<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class LocationMaster extends Model
{
    protected $fillable = [
        'code_loc',
        'name',
    ];

    public function financeBranches(): HasMany
    {
        return $this->hasMany(FinanceBranch::class);
    }

    public function customers(): HasManyThrough
    {
        return $this->hasManyThrough(CustomerData::class, FinanceBranch::class);
    }
}
