<?php

namespace App\Policies;

use App\Models\Rating;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RatingPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('rating-index');
    }

    public function view(User $user, Rating $rating): bool
    {

        return $user->hasPermissionTo($rating, 'rating-index');
    }

    public function create(User $user): bool
    {

        return $user->hasPermissionTo('rating-create');
    }

    public function update(User $user, Rating $rating): bool
    {
        return $user->hasPermissionTo($rating, 'rating-update');
    }

    public function delete(User $user, Rating $rating): bool
    {
        return $user->hasPermissionTo($rating, 'rating-destroy');
    }

    public function restore(User $user, Rating $rating): bool
    {
        return $user->hasPermissionTo($rating, 'rating-restore');
    }

    public function forceDelete(User $user, Rating $rating): bool
    {
        return $user->hasPermissionTo($rating, 'rating-force-delete');
    }
}
