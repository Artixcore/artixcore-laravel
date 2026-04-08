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
            ['filament.access', 'users.manage_roles', 'portal.access', 'micro_tool_analytics.view', 'micro_tools.manage_monetization'],
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
            $crud('products'),
            $crud('team_profiles'),
            $crud('taxonomies'),
            $crud('terms'),
            $crud('analytics_events'),
            $crud('users'),
            $crud('site_settings'),
            $crud('media'),
            $crud('services'),
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
            'admin' => $internalAll,
            'content_admin' => $pick(array_merge(
                ['filament.access'],
                $crud('pages'),
                $crud('page_blocks'),
                $crud('nav_menus'),
                $crud('nav_items'),
                $crud('articles'),
                $crud('case_studies'),
                $crud('site_settings'),
                $crud('media'),
                $crud('services'),
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
            )),
            'marketing_admin' => $pick(array_merge(
                ['filament.access'],
                $crud('pages'),
                $crud('page_blocks'),
                $crud('nav_menus'),
                $crud('nav_items'),
                $crud('articles'),
                $crud('products'),
                $crud('case_studies'),
                $crud('team_profiles'),
                $crud('site_settings'),
                $crud('media'),
                $crud('services'),
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
            )),
            'researcher_admin' => $pick(array_merge(
                ['filament.access'],
                $crud('research_papers'),
                ['articles.view_any', 'articles.view', 'pages.view_any', 'pages.view'],
            )),
            'designer_admin' => $pick(array_merge(
                ['filament.access'],
                $crud('pages'),
                $crud('page_blocks'),
                $crud('nav_menus'),
                $crud('nav_items'),
                $crud('site_settings'),
                $crud('media'),
            )),
            'developer_admin' => $pick(array_merge(
                ['filament.access'],
                $crud('analytics_events'),
                $crud('taxonomies'),
                $crud('terms'),
                ['pages.view_any', 'pages.view'],
            )),
            'engineer_admin' => $pick(array_merge(
                ['filament.access'],
                $crud('analytics_events'),
                $crud('taxonomies'),
                $crud('terms'),
                ['pages.view_any', 'pages.view'],
            )),
            'hr_admin' => $pick(array_merge(
                ['filament.access'],
                $crud('team_profiles'),
                ['pages.view_any', 'pages.view'],
            )),
            'support_admin' => $pick([
                'filament.access',
                'users.view_any',
                'users.view',
                'analytics_events.view_any',
                'analytics_events.view',
            ]),
            'finance_admin' => $pick([
                'filament.access',
                'analytics_events.view_any',
                'analytics_events.view',
            ]),
            'agentic_ai_admin' => $pick(array_merge(
                ['filament.access'],
                $crud('ai_agents'),
                $crud('ai_workflows'),
                $crud('ai_runs'),
                $crud('ai_run_logs'),
                $crud('ai_approvals'),
            )),
            'end_user' => $pick(['portal.access']),
            'client' => $pick(['portal.access']),
            'article_writer' => $pick(['portal.access']),
            'contributor' => $pick(['portal.access']),
        ];

        foreach ($roles as $roleName => $permissionNames) {
            $role = Role::findOrCreate($roleName, self::GUARD);
            $role->syncPermissions($permissionNames);
        }
    }
}
