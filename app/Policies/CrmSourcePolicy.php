<?php

namespace App\Policies;

use App\Models\CrmSource;
use App\Models\User;

class CrmSourcePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('crm.view');
    }

    public function view(User $user, CrmSource $crmSource): bool
    {
        return $user->can('crm.view');
    }

    public function create(User $user): bool
    {
        return $user->can('crm.sources.manage');
    }

    public function update(User $user, CrmSource $crmSource): bool
    {
        return $user->can('crm.sources.manage');
    }

    public function delete(User $user, CrmSource $crmSource): bool
    {
        return $user->can('crm.sources.manage');
    }
}
