<?php

namespace App\Policies;

use App\Models\FinanceBranch;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FinanceBranchPolicy
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
        return $user->canDo('FinanceBranchs', 'view');
    }

    public function view(User $user, FinanceBranch $model): bool
    {
        return $user->canDo('FinanceBranchs', 'view');
    }

    public function create(User $user): bool
    {
        return $user->canDo('FinanceBranchs', 'create');
    }

    public function update(User $user, FinanceBranch $model): bool
    {
        return $user->canDo('FinanceBranchs', 'update');
    }

    public function delete(User $user, FinanceBranch $model): bool
    {
        return $user->canDo('FinanceBranchs', 'delete');
    }
}
