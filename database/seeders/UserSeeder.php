<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Default seeded credentials (all use the same password for local/staging bootstrap):
 *
 * Back office (user_kind internal, Filament + permissions per role):
 * - master@artixcore.com — Master Admin
 * - admin@artixcore.com — Admin
 * - dev@artixcore.com — Developer Admin
 * - engineer@artixcore.com — Engineer Admin
 * - hr@artixcore.com — HR Admin
 * - research@artixcore.com — Researcher Admin
 * - marketing@artixcore.com — Marketing Admin
 * - design@artixcore.com — Designer Admin
 * - content@artixcore.com — Content Admin
 * - ai@artixcore.com — AI Admin (agentic_ai_admin)
 * - support@artixcore.com — Support Admin
 * - finance@artixcore.com — Finance Admin
 *
 * Portal / end users (user_kind external, portal.access via role):
 * - user@artixcore.com — End User
 * - client@artixcore.com — Client
 * - writer@artixcore.com — Article Writer
 * - contributor@artixcore.com — Contributor
 *
 * Password for every account above: password123
 */
class UserSeeder extends Seeder
{
    /** @see docblock above — matches bootstrap accounts used in feature tests */
    private const PASSWORD = 'Aws342hfskjdhrw##$%32432';

    public function run(): void
    {
        $rows = [
            ['name' => 'Master Admin', 'email' => 'master@artixcore.com', 'user_kind' => 'internal', 'role' => 'master_admin'],
            ['name' => 'Admin', 'email' => 'admin@artixcore.com', 'user_kind' => 'internal', 'role' => 'admin'],
            ['name' => 'Developer Admin', 'email' => 'dev@artixcore.com', 'user_kind' => 'internal', 'role' => 'developer_admin'],
            ['name' => 'Engineer Admin', 'email' => 'engineer@artixcore.com', 'user_kind' => 'internal', 'role' => 'engineer_admin'],
            ['name' => 'HR Admin', 'email' => 'hr@artixcore.com', 'user_kind' => 'internal', 'role' => 'hr_admin'],
            ['name' => 'Researcher Admin', 'email' => 'research@artixcore.com', 'user_kind' => 'internal', 'role' => 'researcher_admin'],
            ['name' => 'Marketing Admin', 'email' => 'marketing@artixcore.com', 'user_kind' => 'internal', 'role' => 'marketing_admin'],
            ['name' => 'Designer Admin', 'email' => 'design@artixcore.com', 'user_kind' => 'internal', 'role' => 'designer_admin'],
            ['name' => 'Content Admin', 'email' => 'content@artixcore.com', 'user_kind' => 'internal', 'role' => 'content_admin'],
            ['name' => 'AI Admin', 'email' => 'ai@artixcore.com', 'user_kind' => 'internal', 'role' => 'agentic_ai_admin'],
            ['name' => 'Support Admin', 'email' => 'support@artixcore.com', 'user_kind' => 'internal', 'role' => 'support_admin'],
            ['name' => 'Finance Admin', 'email' => 'finance@artixcore.com', 'user_kind' => 'internal', 'role' => 'finance_admin'],
            ['name' => 'End User', 'email' => 'user@artixcore.com', 'user_kind' => 'external', 'role' => 'end_user'],
            ['name' => 'Client', 'email' => 'client@artixcore.com', 'user_kind' => 'external', 'role' => 'client'],
            ['name' => 'Article Writer', 'email' => 'writer@artixcore.com', 'user_kind' => 'external', 'role' => 'article_writer'],
            ['name' => 'Contributor', 'email' => 'contributor@artixcore.com', 'user_kind' => 'external', 'role' => 'contributor'],
        ];

        foreach ($rows as $row) {
            $role = $row['role'];
            unset($row['role']);

            /** @var User $user */
            $user = User::query()->updateOrCreate(
                ['email' => $row['email']],
                [
                    ...$row,
                    'password' => self::PASSWORD,
                    'email_verified_at' => now(),
                    'remember_token' => Str::random(10),
                ],
            );

            $user->syncRoles([$role]);
        }
    }
}
