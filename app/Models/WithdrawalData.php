<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WithdrawalData extends Model
{
    protected $fillable = [
        'customer_data_id',
        'withdrawal_date',
        'status',
        'user_id',
        'vendor_id',
        'bailout_amount',
        'finance_payout',
        'finance_deadline',
        'handling_fee',
        'vendor_fee',
        'estimated_payout',
        'is_finance_paid',
    ];

    protected $casts = [
        'is_finance_paid' => 'boolean',
        'finance_deadline' => 'date',
    ];

    public function bastk()
    {
        return $this->hasOne(BastkRegister::class);
    }

    public function customerData(): BelongsTo
    {
        return $this->belongsTo(CustomerData::class);
    }

    // Shortcut relations to related data from customerData
    public function finance(): BelongsTo
    {
        return $this->customerData->financeBranch->financeMaster();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }
}
