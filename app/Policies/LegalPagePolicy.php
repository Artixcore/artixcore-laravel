<?php

namespace App\Policies;

use App\Models\LegalPage;
use App\Models\User;

class LegalPagePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('legal_pages.view_any');
    }

    public function view(User $user, LegalPage $legalPage): bool
    {
        return $user->can('legal_pages.view');
    }

    public function create(User $user): bool
    {
        return $user->can('legal_pages.create');
    }

    public function update(User $user, LegalPage $legalPage): bool
    {
        return $user->can('legal_pages.update');
    }

    public function delete(User $user, LegalPage $legalPage): bool
    {
        return $user->can('legal_pages.delete');
    }
}
