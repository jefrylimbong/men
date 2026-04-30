<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BastkRegister extends Model
{
    protected $table = 'bastks';

    protected $fillable = [
        'number',
        'status',
        'photos',
        'files',
        'withdrawal_data_id',
    ];

    protected $casts = [
        'status' => 'boolean',
        'photos' => 'array',
        'files' => 'array',
    ];

    public function withdrawalData(): BelongsTo
    {
        return $this->belongsTo(WithdrawalData::class);
    }
}
