<?php

namespace App\Services;

use App\Models\CaseStudy;
use App\Models\NavItem;
use App\Models\NavMenu;
use App\Models\Service;
use Illuminate\Support\Collection;

class WebNavigationService
{
    /**
     * @return list<array{label: string, url: string, children: list<array{label: string, url: string}>, mega: ?string}>
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
     * @param  list<array{label: string, url: string, children?: list<array{label: string, url: string}>, mega?: ?string}>  $primaryLinks
     * @return array{services: Collection<int, Service>, articles: Collection<int, mixed>, caseStudies: Collection<int, CaseStudy>}
     */
    public function megaMenuContext(array $primaryLinks): array
    {
        $needsServices = false;
        $needsPortfolio = false;
        foreach ($primaryLinks as $link) {
            $mega = $link['mega'] ?? null;
            if ($mega === 'services') {
                $needsServices = true;
            }
            if ($mega === 'portfolio') {
                $needsPortfolio = true;
            }
        }

        $services = collect();
        $articles = collect();
        $caseStudies = collect();

        if ($needsServices) {
            $services = Service::query()
                ->published()
                ->with('featuredImageMedia')
                ->orderBy('sort_order')
                ->orderBy('title')
                ->get();
        }

        if ($needsServices || $needsPortfolio) {
            $caseStudies = CaseStudy::query()
                ->published()
                ->orderByDesc('featured')
                ->orderByDesc('published_at')
                ->take(3)
                ->get();
        }

        return [
            'services' => $services,
            'articles' => $articles,
            'caseStudies' => $caseStudies,
        ];
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
     * @return array{label: string, url: string, children: list<array{label: string, url: string}>, mega: ?string}
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

        $mega = null;
        $payload = $item->feature_payload;
        if (is_array($payload) && isset($payload['mega']) && in_array($payload['mega'], ['services', 'portfolio'], true)) {
            $mega = $payload['mega'];
        }

        return [
            'label' => $item->label,
            'url' => $url,
            'children' => $children,
            'mega' => $mega,
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
     * @return list<array{label: string, url: string, children: list<array{label: string, url: string}>, mega: ?string}>
     */
    private function fallbackPrimary(): array
    {
        return [
            ['label' => 'Home', 'url' => '/', 'children' => [], 'mega' => null],
            ['label' => 'Services', 'url' => '/services', 'children' => [], 'mega' => 'services'],
            ['label' => 'SaaS Platforms', 'url' => '/saas-platforms', 'children' => [], 'mega' => null],
            ['label' => 'Portfolio', 'url' => '/portfolio', 'children' => [], 'mega' => 'portfolio'],
            ['label' => 'About', 'url' => '/about', 'children' => [], 'mega' => null],
        ];
    }
}
