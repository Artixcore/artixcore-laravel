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
            'hero_primary_cta_label' => 'Get started',
            'hero_primary_cta_url' => '/get-started',
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
            'hero_primary_cta_label' => 'Get started',
            'hero_primary_cta_url' => '/get-started',
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

    /**
     * @return array<string, mixed>
     */
    public static function defaultSaaSPage(): array
    {
        return [
            'meta_title' => 'SaaS Platforms — Artixcore',
            'meta_description' => 'Design, build, and scale SaaS products: multi-tenant platforms, admin systems, subscriptions, automation, and AI workflows—from idea to production.',
            'hero_badge' => 'SaaS engineering',
            'hero_title' => 'Build SaaS products your customers pay for every month',
            'hero_subtitle' => 'We take you from idea to launch with clear architecture, scalable backends, and polished product UX—then help you grow with automation, integrations, and operational tooling.',
            'hero_primary_cta_label' => 'Book a strategy call',
            'hero_primary_cta_url' => '/contact',
            'hero_secondary_cta_label' => 'View portfolio',
            'hero_secondary_cta_url' => '/portfolio',
            'show_stats' => true,
            'stats_title' => 'Delivery you can plan around',
            'stats' => [
                ['value' => '10+', 'label' => 'Years shipping production systems'],
                ['value' => '120+', 'label' => 'Launches and major releases'],
                ['value' => 'Multi-tenant', 'label' => 'Platforms, dashboards, and admin suites'],
            ],
            'overview_title' => 'What we build for SaaS and platform teams',
            'overview_body' => 'Artixcore designs and ships web-based software that behaves like a real product: sign-up and onboarding, roles and permissions, billing where needed, analytics, and the admin tooling your team uses daily. We work with startups validating an MVP, scale-ups modernizing legacy systems, and enterprises building internal platforms.',
            'offerings_title' => 'SaaS solutions we deliver',
            'offerings_subtitle' => 'End-to-end product engineering focused on recurring revenue, operational efficiency, and maintainable systems.',
            'offerings' => [
                ['title' => 'Custom SaaS development', 'body' => 'Greenfield products from discovery through launch, with a roadmap tied to business outcomes.', 'icon' => 'bi bi-layers'],
                ['title' => 'Multi-tenant platforms', 'body' => 'Tenant isolation, provisioning, plans, and the guardrails that keep data and performance predictable.', 'icon' => 'bi bi-diagram-3'],
                ['title' => 'Internal business systems', 'body' => 'CRMs, ERP-style workflows, ops consoles, and bespoke tools that replace spreadsheets and manual handoffs.', 'icon' => 'bi bi-building-gear'],
                ['title' => 'Admin, CRM & operations UIs', 'body' => 'Dashboards, role-based experiences, and back-office flows your team actually wants to use.', 'icon' => 'bi bi-speedometer2'],
                ['title' => 'AI-powered SaaS', 'body' => 'Smart workflows, assistants, and automation layered on your product without compromising safety and cost.', 'icon' => 'bi bi-stars'],
                ['title' => 'Subscriptions & monetization', 'body' => 'Plans, trials, entitlements, and integration patterns that fit your go-to-market.', 'icon' => 'bi bi-credit-card'],
            ],
            'why_title' => 'Why teams choose Artixcore for SaaS',
            'why_items' => [
                ['title' => 'Architecture first', 'body' => 'We define boundaries, data models, and scaling paths early so you are not rewriting in six months.'],
                ['title' => 'Speed to launch', 'body' => 'Thin vertical slices, staging environments, and weekly demos keep momentum and reduce surprise.'],
                ['title' => 'AI where it earns its place', 'body' => 'Automation and intelligence where they cut cost or improve UX—not buzzword features nobody uses.'],
                ['title' => 'Software you can own', 'body' => 'Documentation, handover-friendly code, and observability baked in—not a black box.'],
            ],
            'features_title' => 'Platform capabilities we implement',
            'features_subtitle' => 'The building blocks that turn a web app into a product business.',
            'features' => [
                ['title' => 'Admin panels & operator tools', 'body' => 'Support, configuration, and moderation workflows tailored to your domain.', 'icon' => 'bi bi-grid-1x2'],
                ['title' => 'Roles, permissions, audit trails', 'body' => 'Fine-grained access and accountability for teams and tenants.', 'icon' => 'bi bi-shield-check'],
                ['title' => 'Subscriptions & billing hooks', 'body' => 'Plans, usage, and provider integration patterns that match your model.', 'icon' => 'bi bi-receipt'],
                ['title' => 'Analytics & reporting', 'body' => 'Product and business metrics your leadership can trust.', 'icon' => 'bi bi-graph-up-arrow'],
                ['title' => 'Dashboards & notifications', 'body' => 'Actionable views, email and in-app signals, and escalation paths.', 'icon' => 'bi bi-bell'],
                ['title' => 'Integrations & APIs', 'body' => 'Webhooks, third-party tools, and partner-facing APIs with clear contracts.', 'icon' => 'bi bi-plug'],
                ['title' => 'Workflow automation', 'body' => 'Background jobs, queues, and rules that remove manual toil.', 'icon' => 'bi bi-lightning-charge'],
            ],
            'process_title' => 'How we build SaaS with you',
            'process_steps' => [
                ['title' => 'Discovery', 'body' => 'We align on users, pricing model, compliance constraints, and what “done” means for v1.'],
                ['title' => 'Architecture & UX', 'body' => 'System design, data model, and flows for customers, admins, and integrations.'],
                ['title' => 'Development', 'body' => 'Iterative builds with reviews, automated checks, and environments you can click through.'],
                ['title' => 'Launch', 'body' => 'Hardening, migration if needed, monitoring, and a controlled rollout.'],
                ['title' => 'Growth support', 'body' => 'Backlog tuning, performance, new modules, and automation as you scale.'],
            ],
            'use_cases_title' => 'Where this work shows up',
            'use_cases' => [
                ['title' => 'Startups', 'body' => 'MVPs and early SaaS products with room to grow without a rewrite.'],
                ['title' => 'Agencies & studios', 'body' => 'White-label or co-branded platforms you deliver to many clients.'],
                ['title' => 'Enterprise tools', 'body' => 'Internal software that matches how your organization actually operates.'],
                ['title' => 'Marketplaces & networks', 'body' => 'Multi-sided products with distinct roles and workflows.'],
                ['title' => 'Education & training', 'body' => 'Portals, cohort tools, and content delivery with clear access control.'],
                ['title' => 'Fintech-adjacent workflows', 'body' => 'Operational platforms with strong audit, permissions, and integration requirements.'],
                ['title' => 'Internal business tools', 'body' => 'Replace fragmented tools with one maintainable system of record.'],
            ],
            'trust_title' => 'Teams that trust our delivery',
            'show_trust_logos' => true,
            'show_case_studies' => true,
            'case_studies_title' => 'Featured work',
            'case_studies_subtitle' => 'Recent platforms and products we have shaped end-to-end.',
            'case_study_limit' => 3,
            'case_study_slugs' => [],
            'service_slugs' => [],
            'highlighted_services_title' => 'Related services',
            'highlighted_services_subtitle' => 'Explore how we support SaaS and platform initiatives across our broader practice.',
            'show_testimonials' => true,
            'testimonials_title' => 'What clients say',
            'faq_section_title' => 'SaaS platform development — FAQ',
            'show_faq' => true,
            'cta_title' => 'Ready to shape your SaaS roadmap?',
            'cta_body' => 'Tell us about your idea, your current stack, and your timeline—we will respond with a practical next step.',
            'cta_primary_label' => 'Contact Artixcore',
            'cta_primary_url' => '/contact',
            'cta_secondary_label' => 'Request a demo discussion',
            'cta_secondary_url' => '/contact',
        ];
    }

    /**
     * @param  array<string, mixed>|null  $stored
     * @return array<string, mixed>
     */
    public static function mergeSaaSPage(?array $stored): array
    {
        return array_replace_recursive(self::defaultSaaSPage(), $stored ?? []);
    }
}
