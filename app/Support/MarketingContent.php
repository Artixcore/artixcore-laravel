<?php

namespace App\Support;

/**
 * Default structured content for homepage / about when DB fields are empty.
 *
 * @phpstan-type HomeBlock array<string, mixed>
 */
final class MarketingContent
{
    /**
     * @return HomeBlock
     */
    public static function defaultHomepage(): array
    {
        return [
            'hero_badge' => 'Premium digital products & engineering',
            'hero_title_prefix' => 'Artixcore builds',
            'hero_typed_phrases' => ['SaaS platforms', 'AI agent systems', 'Web3 solutions', 'Mobile apps', 'Business automation'],
            'hero_subtitle' => 'From discovery to launch, we ship reliable software for teams that need speed, security, and measurable outcomes.',
            'hero_primary_cta_label' => 'Start a project',
            'hero_primary_cta_url' => '/contact',
            'hero_secondary_cta_label' => 'View services',
            'hero_secondary_cta_url' => '/services',
            'hero_stat_value' => '5K+',
            'hero_stat_label' => 'Active users across client products',
            'clients_heading' => 'Teams that trust our delivery',
            'intro_badge' => 'Your partner for product velocity',
            'intro_title' => 'Software, strategy, and execution in one team',
            'intro_body' => 'We combine product thinking with deep engineering across cloud, AI, and decentralized stacks—so you can focus on growth while we harden the stack beneath it.',
            'stat_1_value' => '10+',
            'stat_1_label' => 'Years shipping production systems',
            'stat_2_value' => '120+',
            'stat_2_label' => 'Launches & major releases',
            'services_badge' => 'What we deliver',
            'services_title' => 'Services designed for modern product teams',
            'why_title' => 'Why teams choose Artixcore',
            'why_items' => [
                ['title' => 'ROI-led roadmaps', 'body' => 'Every sprint ties to revenue, retention, or operational efficiency.'],
                ['title' => 'Senior engineers', 'body' => 'No bait-and-switch—experienced leads stay on your account.'],
                ['title' => 'Proven delivery', 'body' => 'Transparent milestones, demos, and documentation you can hand off.'],
            ],
            'process_title' => 'How we work',
            'process_steps' => [
                ['title' => 'Discover', 'body' => 'We align on goals, constraints, and success metrics.'],
                ['title' => 'Design & build', 'body' => 'Iterative delivery with weekly visibility.'],
                ['title' => 'Launch & scale', 'body' => 'Hardening, observability, and continuous improvement.'],
            ],
            'portfolio_title' => 'Featured work',
            'portfolio_subtitle' => 'A snapshot of products and platforms we have shaped end-to-end.',
            'articles_title' => 'Latest insights',
            'articles_subtitle' => 'Notes on engineering, AI, Web3, and product craft.',
            'cta_title' => 'Ready to build something exceptional?',
            'cta_body' => 'Tell us about your roadmap—we will respond with a clear plan and timeline.',
            'cta_button_label' => 'Contact Artixcore',
            'cta_button_url' => '/contact',
            'contact_teaser_title' => 'Let’s talk',
            'contact_teaser_body' => 'Share a few details and we will reach out within two business days.',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function defaultAbout(): array
    {
        return [
            'page_title' => 'About Artixcore',
            'meta_title' => 'About — Artixcore',
            'meta_description' => 'Learn how Artixcore helps companies ship SaaS, AI, mobile, Web3, and automation solutions.',
            'lead' => 'We are a product-minded engineering studio partnering with ambitious teams worldwide.',
            'body_html' => '<p>Artixcore blends strategy, design, and implementation across modern stacks—from multi-tenant SaaS and AI agents to mobile apps and on-chain integrations.</p><p>Our teams work as an extension of yours: transparent communication, pragmatic architecture, and a focus on outcomes you can measure.</p>',
            'mission_title' => 'Mission',
            'mission_body' => 'Accelerate reliable software delivery for organizations building the next generation of digital products.',
            'vision_title' => 'Vision',
            'vision_body' => 'A world where every team can access senior engineering talent without compromising quality or velocity.',
        ];
    }

    /**
     * @param  array<string, mixed>|null  $stored
     * @return array<string, mixed>
     */
    public static function mergeHomepage(?array $stored): array
    {
        return array_replace_recursive(self::defaultHomepage(), $stored ?? []);
    }

    /**
     * @param  array<string, mixed>|null  $stored
     * @return array<string, mixed>
     */
    public static function mergeAbout(?array $stored): array
    {
        return array_replace_recursive(self::defaultAbout(), $stored ?? []);
    }
}
