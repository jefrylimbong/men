<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WithdrawalData;
use Illuminate\Auth\Access\HandlesAuthorization;

class WithdrawalDataPolicy
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
        return $user->canDo('WithdrawalData', 'view');
    }

    public function view(User $user, WithdrawalData $model): bool
    {
        return $user->canDo('WithdrawalData', 'view');
    }

    public function create(User $user): bool
    {
        return $user->canDo('WithdrawalData', 'create');
    }

    public function update(User $user, WithdrawalData $model): bool
    {
        return $user->canDo('WithdrawalData', 'update');
    }

    public function delete(User $user, WithdrawalData $model): bool
    {
        return $user->canDo('WithdrawalData', 'delete');
    }
}
