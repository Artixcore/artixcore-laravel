<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    private const GUARD = 'web';

    /**
     * @return list<string>
     */
    public static function allPermissionNames(): array
    {
        $crud = static fn (string $resource): array => [
            "{$resource}.view_any",
            "{$resource}.view",
            "{$resource}.create",
            "{$resource}.update",
            "{$resource}.delete",
        ];

        return array_values(array_unique(array_merge(
            [
                'filament.access',
                'admin.access',
                'master.access',
                'security.manage',
                'builder.access',
                'builder.publish',
                'builder.ai.use',
                'users.manage_roles',
                'portal.access',
                'micro_tool_analytics.view',
                'micro_tools.manage_monetization',
                'activity_logs.view_any',
                'activity_logs.view',
                'security_settings.view',
                'security_settings.update',
                'articles.publish',
                'ai_articles.generate',
                'case_studies.publish',
                'market_updates.publish',
                'ai_case_studies.generate',
                'ai_market_updates.generate',
            ],
            $crud('micro_tools'),
            $crud('micro_tool_categories'),
            ['micro_tool_runs.view_any', 'micro_tool_runs.view'],
            $crud('pages'),
            $crud('page_blocks'),
            $crud('nav_menus'),
            $crud('nav_items'),
            $crud('articles'),
            $crud('research_papers'),
            $crud('case_studies'),
            $crud('market_updates'),
            $crud('products'),
            $crud('team_profiles'),
            $crud('taxonomies'),
            $crud('terms'),
            $crud('analytics_events'),
            $crud('users'),
            $crud('site_settings'),
            $crud('media'),
            $crud('services'),
            $crud('portfolio_items'),
            $crud('testimonials'),
            $crud('faqs'),
            $crud('contact_messages'),
            $crud('legal_pages'),
            $crud('job_postings'),
            $crud('ai_agents'),
            $crud('ai_workflows'),
            $crud('ai_runs'),
            $crud('ai_run_logs'),
            $crud('ai_approvals'),
            $crud('ai_providers'),
            $crud('ai_conversations'),
            $crud('ai_messages'),
            $crud('leads'),
            [
                'crm.view',
                'crm.create',
                'crm.update',
                'crm.delete',
                'crm.email',
                'crm.sources.manage',
                'crm.projects.manage',
                'reviews.manage',
                'reviews.publish',
                'faqs.manage',
            ],
        )));
    }

    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (self::allPermissionNames() as $name) {
            Permission::findOrCreate($name, self::GUARD);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $all = Permission::query()->where('guard_name', self::GUARD)->pluck('name')->all();

        $internalAll = array_values(array_filter(
            $all,
            static fn (string $name): bool => $name !== 'portal.access'
        ));

        $adminInternal = array_values(array_filter(
            $internalAll,
            static fn (string $name): bool => ! in_array($name, ['master.access', 'security.manage'], true)
        ));

        $pick = static function (array $names) use ($all): array {
            return array_values(array_intersect($all, $names));
        };

        $crud = static fn (string $resource): array => [
            "{$resource}.view_any",
            "{$resource}.view",
            "{$resource}.create",
            "{$resource}.update",
            "{$resource}.delete",
        ];

        $roles = [
            'master_admin' => $internalAll,
            'admin' => $adminInternal,
            'content_admin' => $pick(array_merge(
                ['admin.access', 'builder.access', 'builder.publish', 'builder.ai.use'],
                $crud('pages'),
                $crud('page_blocks'),
                $crud('nav_menus'),
                $crud('nav_items'),
                $crud('articles'),
                $crud('case_studies'),
                $crud('market_updates'),
                $crud('site_settings'),
                $crud('media'),
                $crud('services'),
                $crud('portfolio_items'),
                $crud('testimonials'),
                $crud('faqs'),
                $crud('contact_messages'),
                $crud('legal_pages'),
                $crud('job_postings'),
                $crud('taxonomies'),
                $crud('terms'),
                $crud('micro_tools'),
                $crud('micro_tool_categories'),
                ['micro_tool_analytics.view', 'micro_tool_runs.view_any', 'micro_tool_runs.view'],
                [
                    'articles.publish',
                    'ai_articles.generate',
                    'case_studies.publish',
                    'market_updates.publish',
                    'ai_case_studies.generate',
                    'ai_market_updates.generate',
                ],
                [
                    'crm.view',
                    'crm.create',
                    'crm.update',
                    'crm.email',
                    'reviews.manage',
                    'faqs.manage',
                ],
            )),
            'marketing_admin' => $pick(array_merge(
                ['admin.access', 'builder.access', 'builder.publish', 'builder.ai.use'],
                $crud('pages'),
                $crud('page_blocks'),
                $crud('nav_menus'),
                $crud('nav_items'),
                $crud('articles'),
                $crud('products'),
                $crud('case_studies'),
                $crud('market_updates'),
                $crud('team_profiles'),
                $crud('site_settings'),
                $crud('media'),
                $crud('services'),
                $crud('portfolio_items'),
                $crud('testimonials'),
                $crud('faqs'),
                $crud('contact_messages'),
                $crud('legal_pages'),
                $crud('job_postings'),
                $crud('taxonomies'),
                $crud('terms'),
                $crud('micro_tools'),
                $crud('micro_tool_categories'),
                ['micro_tool_analytics.view', 'micro_tool_runs.view_any', 'micro_tool_runs.view'],
                $crud('leads'),
                ['ai_conversations.view_any', 'ai_conversations.view', 'ai_messages.view_any', 'ai_messages.view'],
                [
                    'articles.publish',
                    'ai_articles.generate',
                    'case_studies.publish',
                    'market_updates.publish',
                    'ai_case_studies.generate',
                    'ai_market_updates.generate',
                ],
                [
                    'crm.view',
                    'crm.create',
                    'crm.update',
                    'crm.email',
                    'crm.sources.manage',
                    'crm.projects.manage',
                    'reviews.manage',
                    'reviews.publish',
                    'faqs.manage',
                ],
            )),
            'researcher_admin' => $pick(array_merge(
                ['admin.access'],
                $crud('research_papers'),
                ['articles.view_any', 'articles.view', 'pages.view_any', 'pages.view'],
            )),
            'designer_admin' => $pick(array_merge(
                ['admin.access', 'builder.access', 'builder.publish', 'builder.ai.use'],
                $crud('pages'),
                $crud('page_blocks'),
                $crud('nav_menus'),
                $crud('nav_items'),
                $crud('site_settings'),
                $crud('media'),
            )),
            'developer_admin' => $pick(array_merge(
                ['admin.access'],
                $crud('analytics_events'),
                $crud('taxonomies'),
                $crud('terms'),
                ['pages.view_any', 'pages.view'],
            )),
            'engineer_admin' => $pick(array_merge(
                ['admin.access'],
                $crud('analytics_events'),
                $crud('taxonomies'),
                $crud('terms'),
                ['pages.view_any', 'pages.view'],
            )),
            'hr_admin' => $pick(array_merge(
                ['admin.access'],
                $crud('team_profiles'),
                ['pages.view_any', 'pages.view'],
            )),
            'support_admin' => $pick([
                'admin.access',
                'users.view_any',
                'users.view',
                'analytics_events.view_any',
                'analytics_events.view',
                'crm.view',
                'leads.view_any',
                'leads.view',
                'leads.update',
                'ai_conversations.view_any',
                'ai_conversations.view',
            ]),
            'finance_admin' => $pick([
                'admin.access',
                'analytics_events.view_any',
                'analytics_events.view',
            ]),
            'agentic_ai_admin' => $pick(array_merge(
                ['admin.access', 'builder.access', 'builder.publish', 'builder.ai.use'],
                $crud('ai_agents'),
                $crud('ai_workflows'),
                $crud('ai_runs'),
                $crud('ai_run_logs'),
                $crud('ai_approvals'),
                $crud('ai_providers'),
                $crud('ai_conversations'),
                $crud('ai_messages'),
                $crud('leads'),
                ['activity_logs.view_any', 'activity_logs.view', 'security_settings.view'],
            )),
            'end_user' => $pick(['portal.access']),
            'client' => $pick(['portal.access']),
            'article_writer' => $pick(array_merge(
                ['admin.access', 'portal.access'],
                $crud('articles'),
                $crud('case_studies'),
                $crud('market_updates'),
                [
                    'articles.publish',
                    'ai_articles.generate',
                    'case_studies.publish',
                    'market_updates.publish',
                    'ai_case_studies.generate',
                    'ai_market_updates.generate',
                ],
            )),
            'contributor' => $pick(['portal.access']),
        ];

        foreach ($roles as $roleName => $permissionNames) {
            $role = Role::findOrCreate($roleName, self::GUARD);
            $role->syncPermissions($permissionNames);
        }
    }
}
