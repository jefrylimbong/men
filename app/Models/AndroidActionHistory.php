<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AndroidActionHistory extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'action',
        'description',
        'duration_seconds',
        'latitude',
        'longitude',
        'address',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
