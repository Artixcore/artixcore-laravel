<?php

namespace App\Policies;

use App\Models\CrmContact;
use App\Models\User;

class CrmContactPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('crm.view');
    }

    public function view(User $user, CrmContact $crmContact): bool
    {
        return $user->can('crm.view');
    }

    public function create(User $user): bool
    {
        return $user->can('crm.create');
    }

    public function update(User $user, CrmContact $crmContact): bool
    {
        return $user->can('crm.update');
    }

    public function delete(User $user, CrmContact $crmContact): bool
    {
        return $user->can('crm.delete');
    }

    public function email(User $user, CrmContact $crmContact): bool
    {
        return $user->can('crm.email');
    }
}
