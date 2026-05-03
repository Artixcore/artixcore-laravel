<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'seed_key' => 'faq.app.timeline',
                'question' => 'How long does app development take?',
                'answer' => 'Timelines depend on scope, integrations, and release strategy. After discovery, Artixcore provides a phased plan with milestones.',
                'category' => 'App Development',
                'sort_order' => 10,
                'is_published' => true,
                'status' => 'published',
                'show_on_general_faq' => true,
                'show_on_saas_page' => false,
            ],
            [
                'seed_key' => 'faq.app.platforms',
                'question' => 'Do you build Android and iOS apps?',
                'answer' => 'Yes—native and cross-platform approaches are available depending on UX, performance, and maintenance constraints.',
                'category' => 'App Development',
                'sort_order' => 11,
                'is_published' => true,
                'status' => 'published',
                'show_on_general_faq' => true,
                'show_on_saas_page' => false,
            ],
            [
                'seed_key' => 'faq.app.backend',
                'question' => 'Can Artixcore build the backend API too?',
                'answer' => 'Yes. Backend APIs, auth patterns, observability, and deployment pipelines are core strengths.',
                'category' => 'App Development',
                'sort_order' => 12,
                'is_published' => true,
                'status' => 'published',
                'show_on_general_faq' => true,
                'show_on_saas_page' => false,
            ],
            [
                'seed_key' => 'faq.app.ai',
                'question' => 'Can you integrate AI features into mobile apps?',
                'answer' => 'Yes—typically via secure backend mediation so keys and policies remain server-side.',
                'category' => 'App Development',
                'sort_order' => 13,
                'is_published' => true,
                'status' => 'published',
                'show_on_general_faq' => true,
                'show_on_saas_page' => false,
            ],
            [
                'seed_key' => 'faq.app.maintenance',
                'question' => 'Do you provide maintenance after launch?',
                'answer' => 'Yes—SLA-based maintenance, upgrades, and iterative delivery are available.',
                'category' => 'App Development',
                'sort_order' => 14,
                'is_published' => true,
                'status' => 'published',
                'show_on_general_faq' => true,
                'show_on_saas_page' => false,
            ],
        ];

        foreach ($items as $row) {
            $key = $row['seed_key'];
            Faq::query()->updateOrCreate(
                ['seed_key' => $key],
                $row
            );
        }
    }
}
