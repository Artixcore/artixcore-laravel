<?php

namespace Database\Seeders;

use App\Models\PortfolioItem;
use Illuminate\Database\Seeder;

class PortfolioSeeder extends Seeder
{
    public function run(): void
    {
        PortfolioItem::query()->updateOrCreate(
            ['slug' => 'commerce-platform-rollout'],
            [
                'title' => 'Commerce platform rollout',
                'client_name' => 'Illustrative retailer network',
                'project_type' => 'E-commerce / integrations',
                'industry' => 'Retail',
                'short_description' => 'Composable storefront and POS-aligned inventory flows delivered iteratively.',
                'body' => '<p>Portfolio stub describing delivery patterns—replace with verified narrative.</p>',
                'challenge' => '<p>Fragmented catalog sources and inconsistent fulfillment visibility.</p>',
                'solution' => '<p>Unified pipelines with disciplined release milestones.</p>',
                'outcome' => '<p>Faster iteration cadence and clearer operational visibility.</p>',
                'technology_stack' => ['Laravel', 'Redis', 'PostgreSQL'],
                'status' => PortfolioItem::STATUS_PUBLISHED,
                'featured' => true,
                'sort_order' => 1,
                'published_at' => now(),
                'meta_title' => 'Commerce platform rollout — portfolio',
                'meta_description' => 'Illustrative portfolio stub for Artixcore showcase grids.',
            ]
        );

        PortfolioItem::query()->updateOrCreate(
            ['slug' => 'internal-ops-dashboard'],
            [
                'title' => 'Internal ops dashboard',
                'client_name' => 'Illustrative operator',
                'project_type' => 'Internal tooling',
                'industry' => 'Operations',
                'short_description' => 'Role-aware dashboards with audit-friendly workflows.',
                'body' => '<p>Portfolio stub for mega menus and cross-links—replace with approved story.</p>',
                'technology_stack' => ['Laravel', 'Livewire'],
                'status' => PortfolioItem::STATUS_PUBLISHED,
                'featured' => false,
                'sort_order' => 2,
                'published_at' => now(),
                'meta_title' => 'Internal ops dashboard — portfolio',
                'meta_description' => 'Illustrative portfolio stub.',
            ]
        );
    }
}
