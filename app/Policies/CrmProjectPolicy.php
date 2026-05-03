<?php

namespace App\Policies;

use App\Models\CrmProject;
use App\Models\User;

class CrmProjectPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('crm.view');
    }

    public function view(User $user, CrmProject $crmProject): bool
    {
        return $user->can('crm.view');
    }

    public function create(User $user): bool
    {
        return $user->can('crm.projects.manage');
    }

    public function update(User $user, CrmProject $crmProject): bool
    {
        return $user->can('crm.projects.manage');
    }

    public function delete(User $user, CrmProject $crmProject): bool
    {
        return $user->can('crm.projects.manage');
    }
}
