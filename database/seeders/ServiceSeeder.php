<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ServiceSeeder extends Seeder
{
    /**
     * Idempotent canonical services for Artixcore (editable after seed).
     */
    public function run(): void
    {
        $rows = [
            ['slug' => 'web-development', 'title' => 'Web Development', 'summary' => 'Modern web apps, performance, and scalable frontends.'],
            ['slug' => 'app-development', 'title' => 'App Development', 'summary' => 'Native and cross-platform mobile apps with secure backends.'],
            ['slug' => 'saas-development', 'title' => 'SaaS Development', 'summary' => 'Multi-tenant SaaS, billing, onboarding, and operations tooling.'],
            ['slug' => 'ai-software-development', 'title' => 'AI Software Development', 'summary' => 'Agents, automation, RAG, and AI-assisted workflows.'],
            ['slug' => 'e-commerce-development', 'title' => 'E-commerce Development', 'summary' => 'Stores, POS integrations, catalog pipelines, and payments.'],
            ['slug' => 'crm-erp-development', 'title' => 'CRM / ERP Development', 'summary' => 'Custom CRM/ERP glue, workflows, and data integrations.'],
            ['slug' => 'ui-ux-design', 'title' => 'UI/UX Design', 'summary' => 'Product UX, design systems, and conversion-focused interfaces.'],
            ['slug' => 'automation', 'title' => 'Automation', 'summary' => 'Business automation: integrations, ETL, monitoring, and runbooks.'],
            ['slug' => 'api-development', 'title' => 'API Development', 'summary' => 'Versioned APIs, gateways, auth patterns, and partner integrations.'],
            ['slug' => 'cloud-devops', 'title' => 'Cloud / DevOps', 'summary' => 'Cloud foundations, CI/CD, observability, and reliability.'],
            ['slug' => 'maintenance-support', 'title' => 'Maintenance & Support', 'summary' => 'SLAs, upgrades, incident response, and continuous improvements.'],
        ];

        foreach ($rows as $i => $row) {
            Service::query()->updateOrCreate(
                ['slug' => $row['slug']],
                [
                    'title' => $row['title'],
                    'summary' => $row['summary'],
                    'body' => '<p>'.e($row['summary']).' Replace this overview with production narrative.</p>',
                    'benefits' => [['title' => 'Outcome-focused delivery'], ['title' => 'Clear milestones']],
                    'process' => [['title' => 'Discover'], ['title' => 'Build'], ['title' => 'Ship']],
                    'technologies' => [['name' => 'Laravel'], ['name' => 'React']],
                    'icon' => 'bi bi-braces',
                    'featured' => $i < 3,
                    'sort_order' => $i,
                    'status' => 'published',
                    'published_at' => now(),
                    'meta_title' => $row['title'].' — Artixcore',
                    'meta_description' => Str::limit(strip_tags($row['summary']), 155),
                ]
            );
        }
    }
}
