<?php

namespace App\Policies;

use App\Models\User;
use App\Policies\Concerns\ChecksFilamentCrudPermissions;

class MediaAssetPolicy
{
    use ChecksFilamentCrudPermissions;

    protected static function permissionPrefix(): string
    {
        return 'media';
    }
}
