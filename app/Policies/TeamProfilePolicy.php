<?php

namespace App\Policies;

use App\Models\TeamProfile;
use App\Models\User;

class TeamProfilePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('team_profiles.view_any');
    }

    public function view(?User $user, TeamProfile $teamProfile): bool
    {
        if ($user !== null && $user->can('team_profiles.view')) {
            return true;
        }

        if ($teamProfile->status !== 'published') {
            return false;
        }

        return ! $teamProfile->published_at || $teamProfile->published_at->lte(now());
    }

    public function create(User $user): bool
    {
        return $user->can('team_profiles.create');
    }

    public function update(User $user, TeamProfile $teamProfile): bool
    {
        return $user->can('team_profiles.update');
    }

    public function delete(User $user, TeamProfile $teamProfile): bool
    {
        return $user->can('team_profiles.delete');
    }
}
