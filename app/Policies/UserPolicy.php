<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('reply-index');
    }

    public function delete(User $user): bool
    {
        return $user->hasPermissionTo('user-destroy');
    }

    public function block(User $user): bool
    {
        return $user->hasPermissionTo('user-block');
    }

    public function unblock(User $user): bool
    {
        return $user->hasPermissionTo('user-unblock');
    }

    public function action(User $user): bool
    {
        return $user->hasPermissionTo('user-action');
    }
}
