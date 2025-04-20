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
        //
    }

    public function view(User $user, Rating $rating): bool {}

    public function create(User $user): bool {}

    public function update(User $user, Rating $rating): bool {}

    public function delete(User $user, Rating $rating): bool {}

    public function restore(User $user, Rating $rating): bool {}

    public function forceDelete(User $user, Rating $rating): bool {}
}
