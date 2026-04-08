<?php

namespace Database\Seeders;

use App\Models\Faq;
use App\Models\JobPosting;
use App\Models\LegalPage;
use App\Models\NavItem;
use App\Models\NavMenu;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\Testimonial;
use App\Support\MarketingContent;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MarketingBladeSeeder extends Seeder
{
    public function run(): void
    {
        $settings = SiteSetting::instance();
        $settings->fill([
            'site_name' => $settings->site_name ?: 'Artixcore',
            'default_meta_title' => $settings->default_meta_title ?: 'Artixcore — SaaS, AI, Web3 & custom software',
            'default_meta_description' => $settings->default_meta_description ?: 'We build SaaS platforms, AI agent systems, mobile apps, Web3 integrations, and business automation for teams that need velocity and quality.',
            'contact_email' => $settings->contact_email ?: 'hello@artixcore.test',
            'homepage_content' => MarketingContent::mergeHomepage($settings->homepage_content),
            'about_content' => MarketingContent::mergeAbout($settings->about_content),
        ]);
        $settings->save();

        $menu = NavMenu::query()->firstOrCreate(
            ['key' => 'web_primary'],
            ['name' => 'Marketing header']
        );

        NavItem::query()->where('nav_menu_id', $menu->id)->delete();

        foreach (
            [
                ['Home', '/', 0],
                ['Services', '/services', 1],
                ['Portfolio', '/portfolio', 2],
                ['Blog', '/blog', 3],
                ['About', '/about', 4],
                ['Careers', '/careers', 5],
                ['FAQ', '/faq', 6],
                ['Contact', '/contact', 7],
            ] as [$label, $url, $order]
        ) {
            NavItem::query()->create([
                'nav_menu_id' => $menu->id,
                'parent_id' => null,
                'label' => $label,
                'url' => $url,
                'sort_order' => $order,
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
}
