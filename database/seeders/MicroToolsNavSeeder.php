<?php

namespace Database\Seeders;

use App\Models\NavItem;
use App\Models\NavMenu;
use Illuminate\Database\Seeder;

/**
 * Idempotent: adds Micro Tools to the primary header nav when missing.
 * Use on databases seeded before Micro Tools was added to ContentSeeder:
 * `php artisan db:seed --class=MicroToolsNavSeeder`
 */
class MicroToolsNavSeeder extends Seeder
{
    public function run(): void
    {
        $primary = NavMenu::query()->where('key', 'primary')->first();
        if ($primary === null) {
            return;
        }

        $already = NavItem::query()
            ->where('nav_menu_id', $primary->id)
            ->whereNull('parent_id')
            ->where('label', 'Micro Tools')
            ->exists();

        if ($already) {
            return;
        }

        $microToolsItem = NavItem::query()->create([
            'nav_menu_id' => $primary->id,
            'parent_id' => null,
            'label' => 'Micro Tools',
            'url' => '/micro-tools',
            'page_id' => null,
            'sort_order' => 0,
        ]);

        foreach (
            [
                ['All tools', '/micro-tools'],
                ['Web tools', '/micro-tools/web'],
                ['Domain & DNS', '/micro-tools/domain-dns'],
                ['Security & trust', '/micro-tools/security-trust'],
                ['Media', '/micro-tools/media'],
                ['SEO & content', '/micro-tools/seo-content'],
                ['Developer', '/micro-tools/developer'],
                ['Marketing', '/micro-tools/marketing'],
                ['Favorites & history', '/micro-tools/me'],
            ] as $i => [$label, $url]
        ) {
            NavItem::query()->create([
                'nav_menu_id' => $primary->id,
                'parent_id' => $microToolsItem->id,
                'label' => $label,
                'url' => $url,
                'sort_order' => $i,
            ]);
        }

        NavItem::query()
            ->where('nav_menu_id', $primary->id)
            ->whereNull('parent_id')
            ->where('id', '!=', $microToolsItem->id)
            ->increment('sort_order');
    }
}
