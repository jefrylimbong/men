<?php

namespace App\Policies;

use App\Models\BastkRegister;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BastkRegisterPolicy
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
        return $user->canDo('BastkRegisters', 'view');
    }

    public function view(User $user, BastkRegister $model): bool
    {
        return $user->canDo('BastkRegisters', 'view');
    }

    public function create(User $user): bool
    {
        return $user->canDo('BastkRegisters', 'create');
    }

    public function update(User $user, BastkRegister $model): bool
    {
        return $user->canDo('BastkRegisters', 'update');
    }

    public function delete(User $user, BastkRegister $model): bool
    {
        return $user->canDo('BastkRegisters', 'delete');
    }
}
