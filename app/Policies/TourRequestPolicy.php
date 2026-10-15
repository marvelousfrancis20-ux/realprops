<?php

namespace App\Policies;

use App\Models\TourRequest;
use App\Models\User;

class TourRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isAgent();
    }

    public function view(User $user, TourRequest $tourRequest): bool
    {
        return $user->isAdmin()
            || $tourRequest->agent_id === $user->id
            || $tourRequest->user_id === $user->id;
    }

    public function update(User $user, TourRequest $tourRequest): bool
    {
        return $user->isAdmin() || $tourRequest->agent_id === $user->id;
    }

    public function cancel(User $user, TourRequest $tourRequest): bool
    {
        return $user->isAdmin()
            || $tourRequest->agent_id === $user->id
            || $tourRequest->user_id === $user->id;
    }
}
