<?php

namespace App\Policies;

use App\Models\TeamProfile;
use App\Models\User;

class TeamProfilePolicy
{
    public function view(?User $user, TeamProfile $profile): bool
    {
        if ($profile->status !== 'published') {
            return false;
        }

        return ! $profile->published_at || $profile->published_at->lte(now());
    }
}
