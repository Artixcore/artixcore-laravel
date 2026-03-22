<?php

namespace App\Policies;

use App\Models\User;
use App\Policies\Concerns\ChecksFilamentCrudPermissions;

class NavItemPolicy
{
    use ChecksFilamentCrudPermissions;

    protected static function permissionPrefix(): string
    {
        return 'nav_items';
    }
}
