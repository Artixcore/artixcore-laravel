<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    private const GUARD = 'web';

    /**
     * @return list<array{name: string, slug: string, description: string}>
     */
    public static function roleDefinitions(): array
    {
        return [
            [
                'name' => 'master_admin',
                'slug' => 'master-admin',
                'description' => 'Full platform access; highest privileges including all back-office permissions.',
            ],
            [
                'name' => 'admin',
                'slug' => 'admin',
                'description' => 'General back-office administrator with broad internal permissions.',
            ],
            [
                'name' => 'developer_admin',
                'slug' => 'developer-admin',
                'description' => 'Developer-focused admin: analytics, taxonomies, terms, and read-only content visibility.',
            ],
            [
                'name' => 'engineer_admin',
                'slug' => 'engineer-admin',
                'description' => 'Engineering admin: analytics, taxonomies, terms, and read-only content visibility.',
            ],
            [
                'name' => 'hr_admin',
                'slug' => 'hr-admin',
                'description' => 'HR admin: team profiles and read-only pages.',
            ],
            [
                'name' => 'researcher_admin',
                'slug' => 'researcher-admin',
                'description' => 'Research admin: research papers and read-only articles/pages.',
            ],
            [
                'name' => 'marketing_admin',
                'slug' => 'marketing-admin',
                'description' => 'Marketing admin: pages, navigation, articles, products, case studies, and team profiles.',
            ],
            [
                'name' => 'designer_admin',
                'slug' => 'designer-admin',
                'description' => 'Design admin: pages, blocks, navigation, site settings, and media.',
            ],
            [
                'name' => 'content_admin',
                'slug' => 'content-admin',
                'description' => 'Content admin: pages, blocks, navigation, and articles.',
            ],
            [
                'name' => 'support_admin',
                'slug' => 'support-admin',
                'description' => 'Support admin: user directory (view) and analytics visibility.',
            ],
            [
                'name' => 'finance_admin',
                'slug' => 'finance-admin',
                'description' => 'Finance admin: analytics visibility and Filament access.',
            ],
            [
                'name' => 'agentic_ai_admin',
                'slug' => 'ai-admin',
                'description' => 'Agentic System AI Admin: AI agents, workflows, runs, logs, and approvals.',
            ],
            [
                'name' => 'end_user',
                'slug' => 'end-user',
                'description' => 'Standard platform end user with portal access.',
            ],
            [
                'name' => 'client',
                'slug' => 'client',
                'description' => 'External client account with portal access.',
            ],
            [
                'name' => 'article_writer',
                'slug' => 'article-writer',
                'description' => 'Contributor focused on articles; portal access (extend with article permissions as APIs evolve).',
            ],
            [
                'name' => 'contributor',
                'slug' => 'contributor',
                'description' => 'General contributor with portal access.',
            ],
        ];
    }

    public function run(): void
    {
        foreach (self::roleDefinitions() as $row) {
            Role::query()->updateOrCreate(
                [
                    'name' => $row['name'],
                    'guard_name' => self::GUARD,
                ],
                [
                    'slug' => $row['slug'],
                    'description' => $row['description'],
                ],
            );
        }
    }
}
