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
                'question' => 'How long does app development take?',
                'answer' => 'Timelines depend on scope, integrations, and release strategy. After discovery, Artixcore provides a phased plan with milestones.',
                'category' => 'App Development',
                'sort_order' => 10,
                'is_published' => true,
                'show_on_general_faq' => true,
                'show_on_saas_page' => false,
            ],
            [
                'question' => 'Do you build Android and iOS apps?',
                'answer' => 'Yes—native and cross-platform approaches are available depending on UX, performance, and maintenance constraints.',
                'category' => 'App Development',
                'sort_order' => 11,
                'is_published' => true,
                'show_on_general_faq' => true,
                'show_on_saas_page' => false,
            ],
            [
                'question' => 'Can Artixcore build the backend API too?',
                'answer' => 'Yes. Backend APIs, auth patterns, observability, and deployment pipelines are core strengths.',
                'category' => 'App Development',
                'sort_order' => 12,
                'is_published' => true,
                'show_on_general_faq' => true,
                'show_on_saas_page' => false,
            ],
            [
                'question' => 'Can you integrate AI into mobile apps?',
                'answer' => 'Yes—typically via secure backend mediation so keys and policies remain server-side.',
                'category' => 'App Development',
                'sort_order' => 13,
                'is_published' => true,
                'show_on_general_faq' => true,
                'show_on_saas_page' => false,
            ],
            [
                'question' => 'Do you provide maintenance after launch?',
                'answer' => 'Yes—SLA-based maintenance, upgrades, and iterative delivery are available.',
                'category' => 'App Development',
                'sort_order' => 14,
                'is_published' => true,
                'show_on_general_faq' => true,
                'show_on_saas_page' => false,
            ],
        ];

        foreach ($items as $faq) {
            Faq::query()->updateOrCreate(
                ['question' => $faq['question']],
                $faq
            );
        }
    }
}
