<?php

namespace App\Policies;

use App\Models\PaidBastk;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PaidBastkPolicy
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
        return $user->canDo('PaidBastks', 'view');
    }

    public function view(User $user, PaidBastk $model): bool
    {
        return $user->canDo('PaidBastks', 'view');
    }

    public function create(User $user): bool
    {
        return $user->canDo('PaidBastks', 'create');
    }

    public function update(User $user, PaidBastk $model): bool
    {
        return $user->canDo('PaidBastks', 'update');
    }

    public function delete(User $user, PaidBastk $model): bool
    {
        return $user->canDo('PaidBastks', 'delete');
    }
}
