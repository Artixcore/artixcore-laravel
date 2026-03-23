<?php

namespace App\Policies;

use App\Policies\Concerns\ChecksFilamentCrudPermissions;

class MicroToolCategoryPolicy
{
    use ChecksFilamentCrudPermissions;

    protected static function permissionPrefix(): string
    {
        return 'micro_tool_categories';
    }
}
