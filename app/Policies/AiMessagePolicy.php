<?php

namespace App\Policies;

use App\Policies\Concerns\ChecksFilamentCrudPermissions;

class AiMessagePolicy
{
    use ChecksFilamentCrudPermissions;

    protected static function permissionPrefix(): string
    {
        return 'ai_messages';
    }
}
