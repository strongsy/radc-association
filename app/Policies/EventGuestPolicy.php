<?php

namespace App\Policies;

use App\Models\EventGuest;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EventGuestPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        //
    }

    public function view(User $user, EventGuest $eventGuest): bool {}

    public function create(User $user): bool {}

    public function update(User $user, EventGuest $eventGuest): bool {}

    public function delete(User $user, EventGuest $eventGuest): bool {}

    public function restore(User $user, EventGuest $eventGuest): bool {}

    public function forceDelete(User $user, EventGuest $eventGuest): bool {}
}
