<?php

namespace App\Policies;

use App\Policies\Concerns\ChecksFilamentCrudPermissions;

class AiConversationPolicy
{
    use ChecksFilamentCrudPermissions;

    protected static function permissionPrefix(): string
    {
        return 'ai_conversations';
    }
}
