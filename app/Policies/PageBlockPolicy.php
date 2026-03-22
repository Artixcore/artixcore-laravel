<?php

namespace App\Policies;

use App\Models\User;
use App\Policies\Concerns\ChecksFilamentCrudPermissions;

class PageBlockPolicy
{
    use ChecksFilamentCrudPermissions;

    protected static function permissionPrefix(): string
    {
        return 'page_blocks';
    }
}
