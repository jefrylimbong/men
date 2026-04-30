<?php

namespace App\Policies;

use App\Models\LocationMaster;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LocationMasterPolicy
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
        return $user->canDo('LocationMasters', 'view');
    }

    public function view(User $user, LocationMaster $model): bool
    {
        return $user->canDo('LocationMasters', 'view');
    }

    public function create(User $user): bool
    {
        return $user->canDo('LocationMasters', 'create');
    }

    public function update(User $user, LocationMaster $model): bool
    {
        return $user->canDo('LocationMasters', 'update');
    }

    public function delete(User $user, LocationMaster $model): bool
    {
        return $user->canDo('LocationMasters', 'delete');
    }
}
