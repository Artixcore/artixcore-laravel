<?php

namespace App\Policies;

use App\Models\SiteSetting;
use App\Models\User;

class SiteSettingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('site_settings.view_any');
    }

    public function view(User $user, SiteSetting $siteSetting): bool
    {
        return $user->can('site_settings.view');
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, SiteSetting $siteSetting): bool
    {
        return $user->can('site_settings.update');
    }

    public function delete(User $user, SiteSetting $siteSetting): bool
    {
        return false;
    }
}
