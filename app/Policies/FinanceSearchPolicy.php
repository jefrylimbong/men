<?php

namespace App\Policies;

use App\Models\FinanceSearch;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FinanceSearchPolicy
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
        return $user->canDo('FinanceSearchs', 'view');
    }

    public function view(User $user, FinanceSearch $model): bool
    {
        return $user->canDo('FinanceSearchs', 'view');
    }

    public function create(User $user): bool
    {
        return $user->canDo('FinanceSearchs', 'create');
    }

    public function update(User $user, FinanceSearch $model): bool
    {
        return $user->canDo('FinanceSearchs', 'update');
    }

    public function delete(User $user, FinanceSearch $model): bool
    {
        return $user->canDo('FinanceSearchs', 'delete');
    }
}
