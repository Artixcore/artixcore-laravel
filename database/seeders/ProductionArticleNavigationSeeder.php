<?php

namespace Database\Seeders;

use App\Models\NavItem;
use App\Models\NavMenu;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

/**
 * Idempotent: ensures Articles appears in primary header and footer Explore lists.
 * Safe for production; does not delete or reorder other nav items.
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

        $primary = NavMenu::query()->firstOrCreate(
            ['key' => 'web_primary'],
            ['name' => 'Marketing header']
        );

        NavItem::query()->updateOrCreate(
            [
                'nav_menu_id' => $primary->id,
                'parent_id' => null,
                'url' => '/articles',
            ],
            [
                'label' => 'Articles',
                'sort_order' => 5,
                'page_id' => null,
                'feature_payload' => null,
            ]
        );

        $footer = NavMenu::query()->where('key', 'footer')->first();
        if ($footer !== null) {
            NavItem::query()->updateOrCreate(
                [
                    'nav_menu_id' => $footer->id,
                    'parent_id' => null,
                    'url' => '/articles',
                ],
                [
                    'label' => 'Articles',
                    'sort_order' => $this->nextFooterSortOrder($footer->id),
                    'page_id' => null,
                    'feature_payload' => null,
                ]
            );
        }
    }

    private function nextFooterSortOrder(int $menuId): int
    {
        $max = NavItem::query()
            ->where('nav_menu_id', $menuId)
            ->whereNull('parent_id')
            ->max('sort_order');

        return is_numeric($max) ? ((int) $max) + 1 : 0;
    }
}
