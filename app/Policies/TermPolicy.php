<?php

namespace App\Policies;

use App\Models\User;
use App\Policies\Concerns\ChecksFilamentCrudPermissions;

class TermPolicy
{
    use ChecksFilamentCrudPermissions;

    protected static function permissionPrefix(): string
    {
        return 'terms';
    }
}
