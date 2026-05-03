<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SaasPlatformSeeder extends Seeder
{
    public function run(): void
    {
        Product::query()->updateOrCreate(
            ['slug' => 'dealzyro'],
            [
                'title' => 'Dealzyro',
                'tagline' => 'Retail POS, inventory, eCommerce, and backoffice in one platform.',
                'platform_type' => 'Retail SaaS / POS',
                'summary' => 'Dealzyro helps merchants run POS, inventory, online storefronts, affiliates, white-label partners, subscriptions, and payments—with AI-assisted bulk catalog onboarding.',
                'body' => '<p>Dealzyro is an Artixcore SaaS platform for POS, inventory, multi-channel commerce, partner programs (affiliate and white-label), subscriptions, store onboarding, and secure payments.</p>',
                'features' => [
                    ['title' => 'Point of sale', 'body' => 'Fast checkout flows for retail and hybrid commerce.'],
                    ['title' => 'Inventory', 'body' => 'Stock visibility across channels and locations.'],
                    ['title' => 'Online storefront', 'body' => 'Composable storefront tooling aligned with Artixcore delivery patterns.'],
                    ['title' => 'Partners', 'body' => 'Affiliate and white-label pathways for regional operators.'],
                    ['title' => 'Subscriptions', 'body' => 'Packaged plans aligned with merchant maturity.'],
                    ['title' => 'AI-assisted bulk import', 'body' => 'Structured onboarding acceleration—requires operator verification before publishing catalog facts.'],
                ],
                'use_cases' => [
                    ['title' => 'Multi-location retail', 'body' => 'Unified operations across outlets and online sales.'],
                    ['title' => 'Agency / reseller programs', 'body' => 'White-label distribution with governance guardrails.'],
                ],
                'target_audience' => 'SMB retailers, operators launching franchise-style networks, and agencies packaging commerce stacks.',
                'pricing_note' => 'Pricing is tailored by retailer footprint and modules enabled—replace with live packaging.',
                'meta_title' => 'Dealzyro — Artixcore SaaS platform',
                'meta_description' => Str::limit('POS, inventory, eCommerce, affiliates, white-label, subscriptions, AI-assisted onboarding, payments.', 155),
                'status' => 'published',
                'featured' => true,
                'sort_order' => 1,
                'published_at' => now(),
            ]
        );

        Product::query()->updateOrCreate(
            ['slug' => 'prosperofy'],
            [
                'title' => 'Prosperofy',
                'tagline' => 'Backend-first product platform powered by a Laravel core API gateway.',
                'platform_type' => 'Multi-product SaaS core',
                'summary' => 'Prosperofy uses prosperofy-laravel-core as the authoritative backend: standardized /api/auth and /api/app endpoints, modular services, and disciplined frontend integration.',
                'body' => '<p>Prosperofy is engineered backend-first: the Laravel core exposes <code>/api/auth</code> and <code>/api/app</code> as the integration contract. Frontends should consume those endpoints rather than inventing parallel APIs.</p><p>This reduces drift, centralizes security posture, and keeps product modules coherent.</p>',
                'features' => [
                    ['title' => 'Laravel core gateway', 'body' => 'Single backend boundary for authentication and app orchestration.'],
                    ['title' => '/api/auth', 'body' => 'Standard auth flows for integrated clients.'],
                    ['title' => '/api/app', 'body' => 'Application endpoints for modular capabilities.'],
                    ['title' => 'Modular platform structure', 'body' => 'Composable domains without frontend chaos.'],
                ],
                'use_cases' => [
                    ['title' => 'Multi-surface clients', 'body' => 'Web + mobile + partner integrations behind one gateway.'],
                    ['title' => 'Compliance posture', 'body' => 'Centralized auth and policy enforcement on the core.'],
                ],
                'target_audience' => 'Teams shipping multi-product SaaS with strict backend contracts.',
                'pricing_note' => 'Replace with commercial packaging.',
                'meta_title' => 'Prosperofy — Laravel core API gateway',
                'meta_description' => Str::limit('Prosperofy uses a Laravel core backend with /api/auth and /api/app for secure, modular SaaS delivery.', 155),
                'status' => 'published',
                'featured' => true,
                'sort_order' => 2,
                'published_at' => now(),
            ]
        );
    }
}
