<?php

namespace App\Policies;

use App\Models\FinanceMaster;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FinanceMasterPolicy
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
        return $user->canDo('FinanceMasters', 'view');
    }

    public function view(User $user, FinanceMaster $model): bool
    {
        return $user->canDo('FinanceMasters', 'view');
    }

    public function create(User $user): bool
    {
        return $user->canDo('FinanceMasters', 'create');
    }

    public function update(User $user, FinanceMaster $model): bool
    {
        return $user->canDo('FinanceMasters', 'update');
    }

    public function delete(User $user, FinanceMaster $model): bool
    {
        return $user->canDo('FinanceMasters', 'delete');
    }
}
