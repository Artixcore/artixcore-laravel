<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Idempotent repair: ensures Blade/Filament lead permissions exist and are granted to admin roles.
 */
class LeadPermissionSeeder extends Seeder
{
    private const GUARD = 'web';

    /** @var list<string> */
    private const PERMISSIONS = [
        'leads.view_any',
        'leads.view',
        'leads.update',
    ];

    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (self::PERMISSIONS as $name) {
            Permission::findOrCreate($name, self::GUARD);
        }

        foreach (['master_admin', 'admin'] as $roleName) {
            if (! Role::query()->where('name', $roleName)->where('guard_name', self::GUARD)->exists()) {
                continue;
            }

            $role = Role::findByName($roleName, self::GUARD);
            $role->givePermissionTo(self::PERMISSIONS);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
