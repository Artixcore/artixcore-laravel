<?php

namespace Database\Seeders;

use App\Models\Faq;
use App\Models\JobPosting;
use App\Models\LegalPage;
use App\Models\MediaAsset;
use App\Models\NavItem;
use App\Models\NavMenu;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\Testimonial;
use App\Support\MarketingContent;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Marketing header nav (`web_primary`) and default content.
 *
 * Refresh header links after IA changes: `php artisan db:seed --class=MarketingBladeSeeder`
 * Footer menu (`footer`): `php artisan db:seed --class=ContentSeeder` or edit via Admin → Navigation (footer).
 */
class MarketingBladeSeeder extends Seeder
{
    public function run(): void
    {
        $settings = SiteSetting::instance();
        $settings->fill([
            'site_name' => $settings->site_name ?: 'Artixcore',
            'default_meta_title' => $settings->default_meta_title ?: config('marketing.homepage.meta_title'),
            'default_meta_description' => $settings->default_meta_description ?: config('marketing.homepage.meta_description'),
            'contact_email' => $settings->contact_email ?: 'hello@artixcore.test',
            'homepage_content' => MarketingContent::mergeHomepage($settings->homepage_content),
            'about_content' => MarketingContent::mergeAbout($settings->about_content),
            'services_page_content' => MarketingContent::mergeServicesPage($settings->services_page_content),
            'saas_page_content' => MarketingContent::mergeSaaSPage($settings->saas_page_content),
        ]);
        $settings->save();

        $this->seedBrandLogoFromPublicIfPresent($settings);

        $menu = NavMenu::query()->firstOrCreate(
            ['key' => 'web_primary'],
            ['name' => 'Marketing header']
        );

        NavItem::query()->where('nav_menu_id', $menu->id)->delete();

        foreach (
            [
                ['Home', '/', 0, null],
                ['Services', '/services', 1, ['mega' => 'services']],
                ['SaaS Platforms', '/saas-platforms', 2, null],
                ['Portfolio', '/portfolio', 3, ['mega' => 'portfolio']],
                ['About', '/about', 4, null],
            ] as [$label, $url, $order, $featurePayload]
        ) {
            NavItem::query()->create([
                'nav_menu_id' => $menu->id,
                'parent_id' => null,
                'label' => $label,
                'url' => $url,
                'sort_order' => $order,
                'feature_payload' => $featurePayload,
            ]);
        }

        $services = [
            ['SaaS & cloud products', 'bi bi-cloud-arrow-up', 'Multi-tenant platforms, billing, and observability.'],
            ['Web & app development', 'bi bi-phone', 'Modern web apps, APIs, and native mobile experiences.'],
            ['AI agents & automation', 'bi bi-cpu', 'Workflow agents, RAG systems, and internal copilots.'],
            ['Web3 & integrations', 'bi bi-link-45deg', 'Smart contracts, wallets, and chain-adjacent backends.'],
            ['Business automation', 'bi bi-diagram-3', 'CRM/ERP glue, ETL, and low-friction operations tooling.'],
            ['Custom software', 'bi bi-braces', 'Bespoke systems when off-the-shelf tools are not enough.'],
        ];

        foreach ($services as $i => [$title, $icon, $summary]) {
            Service::query()->updateOrCreate(
                ['slug' => Str::slug($title)],
                [
                    'title' => $title,
                    'summary' => $summary,
                    'body' => '<p>'.e($summary).' We scope, build, and ship with clear milestones and documentation.</p>',
                    'icon' => $icon,
                    'sort_order' => $i,
                    'status' => 'published',
                    'published_at' => now(),
                ]
            );
        }

        Testimonial::query()->firstOrCreate(
            ['author_name' => 'Alex Rivera', 'company' => 'Northwind Labs'],
            [
                'role' => 'CTO',
                'body' => 'Artixcore shipped our SaaS rebuild on time with zero drama. Their engineering depth showed in every sprint review.',
                'sort_order' => 0,
                'is_published' => true,
            ]
        );

        Faq::query()->firstOrCreate(
            ['question' => 'How do we start a project?'],
            [
                'answer' => 'Book a short discovery call via the contact form. We respond with a tailored proposal, timeline, and team shape.',
                'sort_order' => 0,
                'is_published' => true,
                'show_on_general_faq' => true,
                'show_on_saas_page' => false,
            ]
        );

        Faq::query()->firstOrCreate(
            ['question' => 'Can you take over an existing SaaS codebase?'],
            [
                'answer' => 'Yes. We start with a focused technical review, then propose a stabilization and roadmap plan—whether you need a rewrite, modular extraction, or incremental hardening.',
                'sort_order' => 1,
                'is_published' => true,
                'show_on_general_faq' => false,
                'show_on_saas_page' => true,
            ]
        );

        LegalPage::query()->updateOrCreate(
            ['slug' => 'privacy-policy'],
            [
                'title' => 'Privacy Policy',
                'body' => '<p>This placeholder policy should be replaced with your legal text. Artixcore respects user privacy and processes data only as needed to deliver services.</p>',
                'meta_title' => 'Privacy Policy',
                'meta_description' => 'Artixcore privacy policy.',
            ]
        );

        LegalPage::query()->updateOrCreate(
            ['slug' => 'terms-and-conditions'],
            [
                'title' => 'Terms & Conditions',
                'body' => '<p>Replace this placeholder with your terms of service. By using this site you agree to the terms published here.</p>',
                'meta_title' => 'Terms & Conditions',
                'meta_description' => 'Artixcore terms and conditions.',
            ]
        );

        JobPosting::query()->firstOrCreate(
            ['title' => 'Senior Full-Stack Engineer'],
            [
                'location' => 'Remote',
                'employment_type' => 'Full-time',
                'body' => '<p>We are looking for engineers who love Laravel, TypeScript, and shipping. Apply via the contact form with your portfolio.</p>',
                'sort_order' => 0,
                'is_published' => true,
            ]
        );
    }

    private function seedBrandLogoFromPublicIfPresent(SiteSetting $settings): void
    {
        $src = null;
        foreach ([public_path('logo.png'), public_path('logo.PNG')] as $path) {
            if (is_file($path)) {
                $src = $path;
                break;
            }
        }
        if ($src === null) {
            return;
        }

        Storage::disk('public')->makeDirectory('media/brand');
        $destRelative = 'media/brand/'.basename($src);
        Storage::disk('public')->put($destRelative, File::get($src));

        $mime = @mime_content_type($src) ?: 'image/png';
        $media = MediaAsset::query()->updateOrCreate(
            ['path' => $destRelative],
            [
                'disk' => 'public',
                'directory' => 'media/brand',
                'filename' => basename($src),
                'mime_type' => $mime,
                'size_bytes' => (int) filesize($src),
                'alt_text' => ($settings->site_name ?: 'Site').' logo',
            ]
        );

        if ($settings->logo_media_id === null) {
            $settings->forceFill(['logo_media_id' => $media->id])->save();
        }
    }
}
