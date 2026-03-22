<?php

namespace App\Policies;

use App\Models\User;
use App\Policies\Concerns\ChecksFilamentCrudPermissions;

class AnalyticsEventPolicy
{
    use ChecksFilamentCrudPermissions;

    protected static function permissionPrefix(): string
    {
        return 'analytics_events';
    }
}
