<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function before(User $user, string $ability): ?bool
    {
        if ($user->type === 'superadmin' || str_contains((string) $user->type, 'superadmin')) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->canDo('Users', 'view');
    }

    public function view(User $user, User $model): bool
    {
        return $user->canDo('Users', 'view');
    }

    public function create(User $user): bool
    {
        return $user->canDo('Users', 'create');
    }

    public function update(User $user, User $model): bool
    {
        return $user->canDo('Users', 'update');
    }

    public function delete(User $user, User $model): bool
    {
        return $user->canDo('Users', 'delete');
    }
}
