<?php

namespace Database\Seeders;

use App\Models\HomepageSection;
use App\Models\SiteSetting;
use App\Support\MarketingContent;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class HomepageSeeder extends Seeder
{
    public function run(): void
    {
        if (! Schema::hasTable('homepage_sections')) {
            return;
        }

        $d = MarketingContent::defaultHomepage();

        $this->seedHomepageSeo();
        $this->seedSections($d);
    }

    private function seedHomepageSeo(): void
    {
        if (! Schema::hasTable('site_settings')) {
            return;
        }

        try {
            $site = SiteSetting::instance();
        } catch (\Throwable) {
            return;
        }

        $existing = $site->homepage_seo;
        $hasContent = is_array($existing) && count(array_filter(
            $existing,
            static fn ($v) => $v !== null && $v !== ''
        )) > 0;

        if ($hasContent) {
            return;
        }

        $site->homepage_seo = [
            'meta_title' => (string) config('marketing.homepage.meta_title'),
            'meta_description' => (string) config('marketing.homepage.meta_description'),
            'meta_keywords' => (string) config('marketing.default_keywords'),
            'canonical_url' => rtrim((string) config('app.url', url('/')), '/').'/',
            'robots' => 'index, follow',
            'og_title' => (string) config('marketing.homepage.og_title', config('marketing.homepage.meta_title')),
            'og_description' => (string) config('marketing.homepage.og_description', config('marketing.homepage.meta_description')),
            'og_image' => null,
            'twitter_title' => null,
            'twitter_description' => null,
            'twitter_image' => null,
        ];
        $site->save();
    }

    /**
     * @param  array<string, mixed>  $d
     */
    private function seedSections(array $d): void
    {
        HomepageSection::query()->firstOrCreate(
            ['key' => 'hero'],
            [
                'title' => $d['hero_title'] ?? null,
                'subtitle' => $d['hero_subtitle'] ?? null,
                'badge_text' => $d['hero_badge'] ?? null,
                'button_text' => $d['hero_primary_cta_label'] ?? null,
                'button_url' => $d['hero_primary_cta_url'] ?? '/lead',
                'secondary_button_text' => $d['hero_secondary_cta_label'] ?? null,
                'secondary_button_url' => $d['hero_secondary_cta_url'] ?? '/services',
                'is_enabled' => true,
                'sort_order' => 0,
                'settings' => [
                    'hero_trust_line' => $d['hero_trust_line'] ?? null,
                    'hero_stat_value' => $d['hero_stat_value'] ?? null,
                    'hero_stat_label' => $d['hero_stat_label'] ?? null,
                ],
            ]
        );

        HomepageSection::query()->firstOrCreate(
            ['key' => 'trust_metrics'],
            [
                'title' => 'Trust & momentum',
                'is_enabled' => true,
                'sort_order' => 10,
                'settings' => [
                    'metrics' => [
                        ['value' => $d['stat_1_value'] ?? '10+', 'label' => $d['stat_1_label'] ?? 'Years shipping production systems'],
                        ['value' => $d['stat_2_value'] ?? '120+', 'label' => $d['stat_2_label'] ?? 'Launches & major releases'],
                    ],
                ],
            ]
        );

        HomepageSection::query()->firstOrCreate(
            ['key' => 'partner_logos'],
            [
                'title' => $d['clients_heading'] ?? 'Teams that trust our delivery',
                'is_enabled' => true,
                'sort_order' => 20,
                'settings' => [
                    'heading' => $d['clients_heading'] ?? null,
                    'logos' => [],
                ],
            ]
        );

        HomepageSection::query()->firstOrCreate(
            ['key' => 'about'],
            [
                'badge_text' => $d['intro_badge'] ?? null,
                'title' => $d['intro_title'] ?? null,
                'description' => $d['intro_body'] ?? null,
                'is_enabled' => true,
                'sort_order' => 30,
                'settings' => [
                    'stat_1_value' => $d['stat_1_value'] ?? null,
                    'stat_1_label' => $d['stat_1_label'] ?? null,
                    'stat_2_value' => $d['stat_2_value'] ?? null,
                    'stat_2_label' => $d['stat_2_label'] ?? null,
                    'why_items' => $d['why_items'] ?? [],
                    'process_title' => $d['process_title'] ?? null,
                    'process_steps' => $d['process_steps'] ?? [],
                ],
            ]
        );

        HomepageSection::query()->firstOrCreate(
            ['key' => 'featured_services'],
            [
                'badge_text' => $d['services_badge'] ?? null,
                'title' => $d['services_title'] ?? 'Services designed for modern product teams',
                'is_enabled' => true,
                'sort_order' => 40,
                'settings' => [
                    'fallback_limit' => 6,
                ],
            ]
        );

        HomepageSection::query()->firstOrCreate(
            ['key' => 'featured_platforms'],
            [
                'title' => 'SaaS platforms',
                'subtitle' => 'Products and platforms we ship end-to-end.',
                'is_enabled' => true,
                'sort_order' => 50,
                'settings' => [
                    'fallback_limit' => 6,
                ],
            ]
        );

        HomepageSection::query()->firstOrCreate(
            ['key' => 'featured_portfolio'],
            [
                'title' => $d['portfolio_title'] ?? 'Featured work',
                'subtitle' => $d['portfolio_subtitle'] ?? null,
                'is_enabled' => true,
                'sort_order' => 60,
                'settings' => [
                    'fallback_limit' => 6,
                ],
            ]
        );

        HomepageSection::query()->firstOrCreate(
            ['key' => 'featured_case_studies'],
            [
                'title' => 'Case studies',
                'subtitle' => 'Selected delivery highlights.',
                'is_enabled' => true,
                'sort_order' => 70,
                'settings' => [
                    'fallback_limit' => 6,
                ],
            ]
        );

        HomepageSection::query()->firstOrCreate(
            ['key' => 'latest_articles'],
            [
                'title' => $d['articles_title'] ?? 'Latest insights',
                'subtitle' => $d['articles_subtitle'] ?? null,
                'is_enabled' => true,
                'sort_order' => 80,
                'settings' => [
                    'auto_limit' => 3,
                ],
            ]
        );

        HomepageSection::query()->firstOrCreate(
            ['key' => 'testimonials'],
            [
                'title' => 'What clients say',
                'is_enabled' => true,
                'sort_order' => 90,
                'settings' => [
                    'fallback_limit' => 12,
                ],
            ]
        );

        HomepageSection::query()->firstOrCreate(
            ['key' => 'faq'],
            [
                'title' => 'Frequently asked questions',
                'is_enabled' => true,
                'sort_order' => 100,
                'settings' => [
                    'fallback_limit' => 8,
                ],
            ]
        );

        HomepageSection::query()->firstOrCreate(
            ['key' => 'final_cta'],
            [
                'title' => $d['cta_title'] ?? 'Ready to build something exceptional?',
                'description' => $d['cta_body'] ?? null,
                'button_text' => $d['cta_button_label'] ?? 'Contact Artixcore',
                'button_url' => $d['cta_button_url'] ?? '/lead',
                'is_enabled' => true,
                'sort_order' => 110,
                'settings' => [
                    'contact_teaser_title' => $d['contact_teaser_title'] ?? null,
                    'contact_teaser_body' => $d['contact_teaser_body'] ?? null,
                ],
            ]
        );
    }
}
