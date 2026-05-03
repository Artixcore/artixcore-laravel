<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Creates a master admin only when MASTER_ADMIN_EMAIL and MASTER_ADMIN_PASSWORD are set in the environment.
 */
class MasterAdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('MASTER_ADMIN_EMAIL');
        $password = env('MASTER_ADMIN_PASSWORD');

        if (! is_string($email) || trim($email) === '' || ! is_string($password) || $password === '') {
            return;
        }

        /** @var User $user */
        $user = User::query()->updateOrCreate(
            ['email' => strtolower(trim($email))],
            [
                'name' => 'Master Admin',
                'user_kind' => 'internal',
                'password' => Hash::make($password),
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ],
        );

        $user->syncRoles(['master_admin']);
    }
}
