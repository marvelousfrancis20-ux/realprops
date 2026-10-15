<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('users.manage');
    }

    public function view(User $user, User $model): bool
    {
        return $user->can('users.manage') || $user->id === $model->id;
    }

    public function update(User $user, User $model): bool
    {
        return $user->can('users.manage') || $user->id === $model->id;
    }

    public function delete(User $user, User $model): bool
    {
        return $user->can('users.manage') && $user->id !== $model->id;
    }

    public function suspend(User $user, User $model): bool
    {
        return $user->can('users.manage') && $user->id !== $model->id;
    }

    public function changeRole(User $user, User $model): bool
    {
        return $user->can('users.manage');
    }
}
