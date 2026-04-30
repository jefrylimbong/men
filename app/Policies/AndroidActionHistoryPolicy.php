<?php

namespace App\Policies;

use App\Models\AndroidActionHistory;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AndroidActionHistoryPolicy
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
        return $user->canDo('AndroidActionHistories', 'view');
    }

    public function view(User $user, AndroidActionHistory $model): bool
    {
        return $user->canDo('AndroidActionHistories', 'view');
    }

    public function create(User $user): bool
    {
        return $user->canDo('AndroidActionHistories', 'create');
    }

    public function update(User $user, AndroidActionHistory $model): bool
    {
        return $user->canDo('AndroidActionHistories', 'update');
    }

    public function delete(User $user, AndroidActionHistory $model): bool
    {
        return $user->canDo('AndroidActionHistories', 'delete');
    }
}
