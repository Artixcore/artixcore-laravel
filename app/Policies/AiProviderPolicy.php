<?php

namespace App\Policies;

use App\Policies\Concerns\ChecksFilamentCrudPermissions;

class AiProviderPolicy
{
    use ChecksFilamentCrudPermissions;

    protected static function permissionPrefix(): string
    {
        return 'ai_providers';
    }
}
