<?php

namespace Database\Seeders;

use App\Models\NavItem;
use App\Models\NavMenu;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

/**
 * Idempotent: fixes legacy /resources/* nav URLs, ensures Articles exists on web_primary.
 * Safe for production; does not delete other nav items.
 *
 * php artisan db:seed --class=ProductionArticleNavigationSeeder --force
 */
class ProductionArticleNavigationSeeder extends Seeder
{
    public function run(): void
    {
        if (! Schema::hasTable('nav_menus') || ! Schema::hasTable('nav_items')) {
            return;
        }

        $this->normalizeLegacyUrls();

        $primary = NavMenu::query()->firstOrCreate(
            ['key' => 'web_primary'],
            ['name' => 'Marketing header']
        );

        NavItem::query()
            ->where('nav_menu_id', $primary->id)
            ->whereNull('parent_id')
            ->where('label', 'Articles')
            ->update(['url' => '/articles', 'page_id' => null]);

        $hasRootArticlesLink = NavItem::query()
            ->where('nav_menu_id', $primary->id)
            ->whereNull('parent_id')
            ->where('url', '/articles')
            ->exists();

        if (! $hasRootArticlesLink) {
            $maxOrder = (int) NavItem::query()
                ->where('nav_menu_id', $primary->id)
                ->whereNull('parent_id')
                ->max('sort_order');

            NavItem::query()->create([
                'nav_menu_id' => $primary->id,
                'parent_id' => null,
                'label' => 'Articles',
                'url' => '/articles',
                'sort_order' => $maxOrder + 1,
                'page_id' => null,
                'feature_payload' => null,
            ]);
        }
    }

    private function normalizeLegacyUrls(): void
    {
        NavItem::query()->where('url', '/resources/articles')->update([
            'url' => '/articles',
            'page_id' => null,
        ]);

        NavItem::query()->where('url', '/resources/case-studies')->update([
            'url' => '/case-studies',
            'page_id' => null,
        ]);

        NavItem::query()
            ->whereNotNull('page_id')
            ->whereHas('page', fn ($q) => $q->where('path', 'resources/articles'))
            ->update(['url' => '/articles', 'page_id' => null]);

        NavItem::query()
            ->whereNotNull('page_id')
            ->whereHas('page', fn ($q) => $q->where('path', 'resources/case-studies'))
            ->update(['url' => '/case-studies', 'page_id' => null]);
    }
}
