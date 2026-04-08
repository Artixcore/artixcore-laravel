<?php

namespace App\Services;

use App\Models\NavItem;
use App\Models\NavMenu;
use Illuminate\Support\Collection;

class WebNavigationService
{
    /**
     * @return list<array{label: string, url: string, children: list<array{label: string, url: string}>}>
     */
    public function primaryLinks(): array
    {
        $menu = NavMenu::query()->where('key', 'web_primary')->first();
        if ($menu === null) {
            return $this->fallbackPrimary();
        }

        $roots = NavItem::query()
            ->where('nav_menu_id', $menu->id)
            ->whereNull('parent_id')
            ->with(['children' => fn ($q) => $q->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get();

        return $this->filterPublicTree($roots)
            ->map(fn (NavItem $item): array => $this->toLinkArray($item))
            ->values()
            ->all();
    }

    /**
     * @return list<array{label: string, url: string}>
     */
    public function footerLinks(): array
    {
        $menu = NavMenu::query()->where('key', 'footer')->first();
        if ($menu === null) {
            return [];
        }

        $roots = NavItem::query()
            ->where('nav_menu_id', $menu->id)
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->get();

        return $this->filterPublicTree($roots)
            ->map(function (NavItem $item): ?array {
                $url = $item->resolvedPath();
                if ($url === null || $url === '') {
                    return null;
                }

                return ['label' => $item->label, 'url' => $url];
            })
            ->filter()
            ->values()
            ->all();
    }

    /**
     * @return array{label: string, url: string, children: list<array{label: string, url: string}>}
     */
    private function toLinkArray(NavItem $item): array
    {
        $url = $item->resolvedPath() ?? '#';
        $children = [];
        if ($item->relationLoaded('children')) {
            foreach ($item->children as $child) {
                if (! $child->visibleInPublicApi()) {
                    continue;
                }
                $u = $child->resolvedPath();
                if ($u !== null && $u !== '') {
                    $children[] = ['label' => $child->label, 'url' => $u];
                }
            }
        }

        return [
            'label' => $item->label,
            'url' => $url,
            'children' => $children,
        ];
    }

    /**
     * @param  Collection<int, NavItem>  $items
     * @return Collection<int, NavItem>
     */
    private function filterPublicTree(Collection $items): Collection
    {
        return $items
            ->filter(fn (NavItem $item): bool => $item->visibleInPublicApi())
            ->map(function (NavItem $item): NavItem {
                $children = $item->relationLoaded('children')
                    ? $item->children
                    : collect();
                $item->setRelation('children', $this->filterPublicTree($children));

                return $item;
            })
            ->values();
    }

    /**
     * @return list<array{label: string, url: string, children: list<array{label: string, url: string}>}>
     */
    private function fallbackPrimary(): array
    {
        return [
            ['label' => 'Home', 'url' => '/', 'children' => []],
            ['label' => 'Services', 'url' => '/services', 'children' => []],
            ['label' => 'Portfolio', 'url' => '/portfolio', 'children' => []],
            ['label' => 'Blog', 'url' => '/blog', 'children' => []],
            ['label' => 'About', 'url' => '/about', 'children' => []],
            ['label' => 'Careers', 'url' => '/careers', 'children' => []],
            ['label' => 'FAQ', 'url' => '/faq', 'children' => []],
            ['label' => 'Contact', 'url' => '/contact', 'children' => []],
        ];
    }
}
