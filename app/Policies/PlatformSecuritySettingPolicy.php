<?php

namespace App\Policies;

use App\Models\PlatformSecuritySetting;
use App\Models\User;

class PlatformSecuritySettingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('security_settings.view');
    }

    public function view(User $user, PlatformSecuritySetting $platformSecuritySetting): bool
    {
        return $user->can('security_settings.view');
    }

    public function update(User $user, PlatformSecuritySetting $platformSecuritySetting): bool
    {
        return $user->can('security_settings.update');
    }
}
