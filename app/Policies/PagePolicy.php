<?php

namespace App\Policies;

use App\Models\Page;
use App\Models\User;

class PagePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('pages.view_any');
    }

    /**
     * Public API (guest) and Filament (authenticated).
     */
    public function view(?User $user, Page $page): bool
    {
        if ($user !== null && $user->can('pages.view')) {
            return true;
        }

        if ($page->status !== 'published') {
            return false;
        }

        return ! $page->published_at || $page->published_at->lte(now());
    }

    public function create(User $user): bool
    {
        return $user->can('pages.create');
    }

    public function update(User $user, Page $page): bool
    {
        return $user->can('pages.update');
    }

    public function delete(User $user, Page $page): bool
    {
        return $user->can('pages.delete');
    }
}
