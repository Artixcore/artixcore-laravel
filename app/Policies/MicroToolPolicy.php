<?php

namespace App\Policies;

use App\Policies\Concerns\ChecksFilamentCrudPermissions;

class MicroToolPolicy
{
    use ChecksFilamentCrudPermissions;

    protected static function permissionPrefix(): string
    {
        return 'micro_tools';
    }
}
