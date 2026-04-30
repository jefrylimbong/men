<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinanceTransaction extends Model
{
    protected $fillable = [
        'transaction_date',
        'category',
        'amount',
        'debit_type',
        'debit_id',
        'credit_type',
        'credit_id',
        'reference_id',
        'description',
        'status',
    ];

    public function debitEntity()
    {
        return $this->morphTo('debit', 'debit_type', 'debit_id');
    }

    public function creditEntity()
    {
        return $this->morphTo('credit', 'credit_type', 'credit_id');
    }

    public function reference()
    {
        return $this->belongsTo(WithdrawalData::class, 'reference_id');
    }
}
