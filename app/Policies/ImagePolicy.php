<?php

namespace App\Policies;

use App\Models\Image;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ImagePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('image-index');
    }

    public function view(User $user, Image $image): bool
    {
        return $user->hasPermissionTo($image, 'image-index');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('image-create');
    }

    public function update(User $user, Image $image): bool
    {
        return $user->hasPermissionTo($image, 'image-update');
    }

    public function delete(User $user, Image $image): bool
    {
        return $user->hasPermissionTo($image, 'image-destroy');
    }

    public function restore(User $user, Image $image): bool
    {
        return $user->hasPermissionTo($image, 'image-restore');
    }

    public function forceDelete(User $user, Image $image): bool
    {
        return $user->hasPermissionTo($image, 'image-force-delete');
    }
}
