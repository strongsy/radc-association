<?php

namespace App\Policies;

use App\Models\Media;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MediaPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('media-index');
    }

    public function view(User $user, Media $media): bool
    {
        return $user->hasPermissionTo($media,'media-index');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('media-create');
    }

    public function update(User $user, Media $media): bool
    {
        return $user->hasPermissionTo($media,'media-update');
    }

    public function delete(User $user, Media $media): bool
    {
        return $user->hasPermissionTo($media,'media-destroy');
    }

    public function restore(User $user, Media $media): bool
    {
        return $user->hasPermissionTo($media,'media-restore');
    }

    public function forceDelete(User $user, Media $media): bool
    {
        return $user->hasPermissionTo($media,'media-force-delete');
    }
}
