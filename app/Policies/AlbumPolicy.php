<?php

namespace App\Policies;

use App\Models\Album;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AlbumPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('album-index');
    }

    public function view(User $user, Album $album): bool
    {
        return $user->hasPermissionTo($album, 'album-index');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('album-create');
    }

    public function update(User $user, Album $album): bool
    {
        return $user->hasPermissionTo($album, 'album-update');
    }

    public function delete(User $user, Album $album): bool
    {
        return $user->hasPermissionTo($album, 'album-destroy');
    }

    public function restore(User $user, Album $album): bool
    {
        return false;
    }

    public function forceDelete(User $user, Album $album): bool
    {
        return false;
    }
}
