<?php

namespace App\Policies;

use App\Models\Reply;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReplyPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('reply-index');
    }

    public function view(User $user, Reply $replies): bool
    {
        return $user->hasPermissionTo($replies, 'reply-index');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('reply-create');
    }

    public function update(User $user, Reply $replies): bool
    {
        return $user->hasPermissionTo($replies, 'reply-update');
    }

    public function delete(User $user, Reply $replies): bool
    {
        return $user->hasPermissionTo($replies, 'reply-destroy');
    }

    public function restore(User $user, Reply $replies): bool
    {
        return $user->hasPermissionTo($replies, 'reply-restore');
    }

    public function forceDelete(User $user, Reply $replies): bool
    {
        return $user->hasPermissionTo($replies, 'reply-force-delete');
    }
}
