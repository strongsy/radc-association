<?php

namespace App\Policies;

use App\Models\Gallery;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class GalleryPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('gallery-index');
    }

    public function view(User $user, Gallery $gallery): bool
    {
        return $user->hasPermissionTo($gallery,'gallery-index');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('gallery-create');
    }

    public function update(User $user, Gallery $gallery): bool
    {
        return $user->hasPermissionTo($gallery,'gallery-update');
    }

    public function delete(User $user, Gallery $gallery): bool
    {
        return $user->hasPermissionTo($gallery,'gallery-destroy');
    }

    public function restore(User $user, Gallery $gallery): bool
    {
        return $user->hasPermissionTo($gallery,'gallery-restore');
    }

    public function forceDelete(User $user, Gallery $gallery): bool
    {
        return $user->hasPermissionTo($gallery,'gallery-force-delete');
    }
}
