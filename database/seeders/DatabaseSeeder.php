<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RolePermissionSeeder::class);

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        $user->assignRole('master_admin');

        $portalUser = User::factory()->create([
            'name' => 'Portal User',
            'email' => 'portal@example.com',
            'user_kind' => 'external',
        ]);
        $portalUser->assignRole('portal_user');

        $this->call(ContentSeeder::class);
    }
}
