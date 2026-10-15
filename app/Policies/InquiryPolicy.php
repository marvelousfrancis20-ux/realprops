<?php

namespace App\Policies;

use App\Models\Inquiry;
use App\Models\User;

class InquiryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isAgent();
    }

    public function view(User $user, Inquiry $inquiry): bool
    {
        return $user->isAdmin()
            || $inquiry->agent_id === $user->id
            || $inquiry->user_id === $user->id;
    }

    public function reply(User $user, Inquiry $inquiry): bool
    {
        return $user->isAdmin() || $inquiry->agent_id === $user->id;
    }

    public function update(User $user, Inquiry $inquiry): bool
    {
        return $this->reply($user, $inquiry);
    }

    public function delete(User $user, Inquiry $inquiry): bool
    {
        return $user->isAdmin() || $inquiry->agent_id === $user->id;
    }
}
