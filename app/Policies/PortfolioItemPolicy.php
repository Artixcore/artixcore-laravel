<?php

namespace App\Policies;

use App\Models\PortfolioItem;
use App\Models\User;

class PortfolioItemPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('portfolio_items.view_any');
    }

    public function view(?User $user, PortfolioItem $item): bool
    {
        if ($user !== null && $user->can('portfolio_items.view')) {
            return true;
        }

        return $item->status === PortfolioItem::STATUS_PUBLISHED
            && ($item->published_at === null || $item->published_at->lte(now()));
    }

    public function create(User $user): bool
    {
        return $user->can('portfolio_items.create');
    }

    public function update(User $user, PortfolioItem $item): bool
    {
        return $user->can('portfolio_items.update');
    }

    public function delete(User $user, PortfolioItem $item): bool
    {
        return $user->can('portfolio_items.delete');
    }
}
