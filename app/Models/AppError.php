<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppError extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'device_model',
        'device_os',
        'app_version',
        'error_message',
        'stack_trace',
        'page_path',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
