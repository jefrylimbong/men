<?php

namespace App\Policies;

use App\Models\CustomerData;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CustomerDataPolicy
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
        return $user->canDo('CustomerData', 'view');
    }

    public function view(User $user, CustomerData $model): bool
    {
        return $user->canDo('CustomerData', 'view');
    }

    public function create(User $user): bool
    {
        return $user->canDo('CustomerData', 'create');
    }

    public function update(User $user, CustomerData $model): bool
    {
        return $user->canDo('CustomerData', 'update');
    }

    public function delete(User $user, CustomerData $model): bool
    {
        return $user->canDo('CustomerData', 'delete');
    }
}
