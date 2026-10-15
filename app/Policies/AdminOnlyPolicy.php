<?php

namespace App\Policies;

use App\Models\User;

/**
 * Shared admin-only policy for master data + settings models.
 */
class AdminOnlyPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, mixed $model = null): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, mixed $model = null): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, mixed $model = null): bool
    {
        return $user->isAdmin();
    }

    public function restore(User $user, mixed $model = null): bool
    {
        return $user->isAdmin();
    }
}
