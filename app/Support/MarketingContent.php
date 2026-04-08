<?php

namespace App\Support;

/**
 * Default structured content for homepage, about, and services page when DB fields are empty.
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

    /**
     * @return array<string, mixed>
     */
    public static function defaultServicesPage(): array
    {
        return [
            'meta_title' => 'Services — Artixcore',
            'meta_description' => 'SaaS, web and app development, AI agents, Web3, custom software, and business automation from Artixcore.',
            'hero_badge' => 'What we build',
            'hero_title' => 'Services for teams shipping serious software',
            'hero_subtitle' => 'From discovery to production, we deliver SaaS platforms, web and mobile apps, AI agent systems, Web3 integrations, and automation—with clear milestones and senior engineers on the tools.',
            'hero_primary_cta_label' => 'Start a project',
            'hero_primary_cta_url' => '/contact',
            'hero_secondary_cta_label' => 'View portfolio',
            'hero_secondary_cta_url' => '/portfolio',
            'intro_title' => 'One partner across product, platform, and operations',
            'intro_body' => 'Whether you need a net-new product, a rewrite, or targeted acceleration, we embed with your team and ship outcomes you can measure—not endless slide decks.',
            'grid_title' => 'Our comprehensive services',
            'grid_subtitle' => 'Explore how we help you design, build, and run modern digital products.',
            'why_title' => 'Why teams work with us',
            'why_items' => [
                ['title' => 'Depth across stacks', 'body' => 'Cloud-native backends, frontends, mobile, AI, and chain-adjacent systems under one roof.'],
                ['title' => 'Transparent delivery', 'body' => 'Weekly demos, written decisions, and documentation your team can own.'],
                ['title' => 'Built to last', 'body' => 'Security, observability, and maintainability are part of the definition of done.'],
            ],
            'process_title' => 'How we engage',
            'process_steps' => [
                ['title' => 'Discover', 'body' => 'We align on goals, constraints, risks, and success metrics.'],
                ['title' => 'Design & build', 'body' => 'Iterative delivery with tight feedback loops and staging environments.'],
                ['title' => 'Launch & improve', 'body' => 'Rollout, hardening, and continuous improvement with clear ownership.'],
            ],
            'cta_title' => 'Not sure which service fits?',
            'cta_body' => 'Tell us what you are building—we will suggest a practical path, timeline, and team shape.',
            'cta_button_label' => 'Contact Artixcore',
            'cta_button_url' => '/contact',
            'show_testimonials' => true,
        ];
    }

    /**
     * @param  array<string, mixed>|null  $stored
     * @return array<string, mixed>
     */
    public static function mergeServicesPage(?array $stored): array
    {
        return array_replace_recursive(self::defaultServicesPage(), $stored ?? []);
    }
}
