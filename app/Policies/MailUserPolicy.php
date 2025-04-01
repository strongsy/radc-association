<?php

namespace App\Policies;

use App\Models\MailUser;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MailUserPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('reply-index');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('reply-create');
    }

    public function update(User $user, MailUser $mailUser): bool
    {
        return $user->hasPermissionTo($mailUser,'reply-update');
    }

    public function delete(User $user, MailUser $mailUser): bool
    {
        return $user->hasPermissionTo($mailUser,'reply-destroy');
    }

    public function restore(User $user, MailUser $mailUser): bool
    {
        return $user->hasPermissionTo($mailUser,'reply-restore');
    }

    public function forceDelete(User $user, MailUser $mailUser): bool
    {
        return $user->hasPermissionTo($mailUser,'reply-force-delete');
    }
}
