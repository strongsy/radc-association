<?php

namespace App\Policies;

use App\Models\Mail;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MailPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('mail-list');
    }

    public function view(User $user, Mail $mail): bool
    {
        return $user->hasPermissionTo($mail, 'mail-list');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('mail-create');
    }

    public function update(User $user, Mail $mail): bool
    {
        return $user->hasPermissionTo($mail, 'mail-update');
    }

    public function delete(User $user, Mail $mail): bool
    {
        return $user->hasPermissionTo($mail, 'mail-destroy');
    }

    public function restore(User $user, Mail $mail): bool
    {
        return $user->hasPermissionTo($mail, 'mail-restore');
    }

    public function forceDelete(User $user, Mail $mail): bool
    {
        return $user->hasPermissionTo($mail, 'mail-force-delete');
    }
}
