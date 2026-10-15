<?php

namespace App\Policies;

use App\Models\Property;
use App\Models\User;

class PropertyPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Property $property): bool
    {
        return $property->is_approved || $this->update($user, $property);
    }

    public function create(User $user): bool
    {
        return $user->can('properties.create')
            && ! $user->isSuspended()
            && ($user->email_verified_at !== null || ! config('app.require_email_verification_for_agents', true));
    }

    public function update(User $user, Property $property): bool
    {
        if ($user->isSuspended()) {
            return false;
        }

        return $user->can('properties.update')
            && ($user->isAdmin() || $user->id === $property->agent_id);
    }

    public function delete(User $user, Property $property): bool
    {
        if ($user->isSuspended()) {
            return false;
        }

        return $user->can('properties.delete')
            && ($user->isAdmin() || $user->id === $property->agent_id);
    }

    public function approve(User $user, Property $property): bool
    {
        return $user->can('properties.approve');
    }

    public function reject(User $user, Property $property): bool
    {
        return $user->can('properties.approve');
    }

    public function feature(User $user, Property $property): bool
    {
        return $user->can('properties.feature');
    }
}
