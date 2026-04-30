<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable([
    'name',
    'email',
    'password',
    'username',
    'avatar',
    'phone',
    'address',
    'nik',
    'date_birth',
    'place_birth',
    'last_update',
    'access_expired',
    'data_limit',
    'type',
    'permissions',
    'is_active',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    public function financeMasters(): BelongsToMany
    {
        return $this->belongsToMany(FinanceMaster::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_update' => 'date',
            'access_expired' => 'date',
            'type' => 'string',
            'permissions' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function isExpired(): bool
    {
        if ($this->type !== 'user') {
            return false;
        }

        if (! $this->access_expired) {
            return false;
        }

        return $this->access_expired->isPast();
    }

    public function canDo(string $resource, string $action): bool
    {
        if ($this->type === 'superadmin' || str_contains((string) $this->type, 'superadmin')) {
            return true;
        }

        $permissions = $this->permissions[$resource] ?? [];

        return in_array("{$action}_{$resource}", $permissions);
    }
}
