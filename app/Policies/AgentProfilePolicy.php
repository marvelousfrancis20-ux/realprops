<?php

namespace App\Policies;

use App\Models\AgentProfile;
use App\Models\User;

class AgentProfilePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isAgent();
    }

    public function view(User $user, AgentProfile $agentProfile): bool
    {
        return $user->isAdmin() || $agentProfile->user_id === $user->id;
    }

    public function update(User $user, AgentProfile $agentProfile): bool
    {
        return $agentProfile->user_id === $user->id || $user->isAdmin();
    }

    public function verify(User $user, AgentProfile $agentProfile): bool
    {
        return $user->can('agents.verify');
    }
}
