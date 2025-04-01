<?php

namespace App\Policies;

use App\Models\Registrant;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RegistrantPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('registrant-index');
    }

    public function delete(User $user, Registrant $registrant): bool
    {
        return $user->hasPermissionTo($registrant,'registrant-destroy');
    }

    public function authorize(User $user, Registrant $registrant): bool
    {
        return $user->hasPermissionTo($registrant,'registrant-authorize');
    }
}
