<?php

namespace App\Policies;

use App\Models\MarketUpdate;
use App\Models\User;

class MarketUpdatePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('market_updates.view_any');
    }

    public function view(?User $user, MarketUpdate $marketUpdate): bool
    {
        if ($user !== null && $user->can('market_updates.view')) {
            return true;
        }

        if ($marketUpdate->status === MarketUpdate::STATUS_ARCHIVED) {
            return false;
        }

        if ($marketUpdate->status !== MarketUpdate::STATUS_PUBLISHED) {
            return false;
        }

        return ! $marketUpdate->published_at || $marketUpdate->published_at->lte(now());
    }

    public function publish(User $user, MarketUpdate $marketUpdate): bool
    {
        return $user->can('market_updates.publish');
    }

    public function create(User $user): bool
    {
        return $user->can('market_updates.create');
    }

    public function update(User $user, MarketUpdate $marketUpdate): bool
    {
        return $user->can('market_updates.update');
    }

    public function delete(User $user, MarketUpdate $marketUpdate): bool
    {
        return $user->can('market_updates.delete');
    }
}
