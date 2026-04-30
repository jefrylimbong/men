<?php

namespace App\Policies;

use App\Models\UnpaidBastk;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UnpaidBastkPolicy
{
    use HandlesAuthorization;

    public function before(User $user, string $ability): ?bool
    {
        if ($user->type === 'superadmin') {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->canDo('UnpaidBastks', 'view');
    }

    public function view(User $user, UnpaidBastk $model): bool
    {
        return $user->canDo('UnpaidBastks', 'view');
    }

    public function create(User $user): bool
    {
        return $user->canDo('UnpaidBastks', 'create');
    }

    public function update(User $user, UnpaidBastk $model): bool
    {
        return $user->canDo('UnpaidBastks', 'update');
    }

    public function delete(User $user, UnpaidBastk $model): bool
    {
        return $user->canDo('UnpaidBastks', 'delete');
    }
}
