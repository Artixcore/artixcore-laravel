<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\NavigationCollection;
use App\Models\NavItem;
use App\Models\NavMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class NavigationController extends Controller
{
    public function __invoke(Request $request): NavigationCollection
    {
        $key = $request->validate([
            'menu' => 'sometimes|string|in:primary,footer',
        ])['menu'] ?? 'primary';

        $menu = NavMenu::query()->where('key', $key)->firstOrFail();

        $roots = NavItem::query()
            ->where('nav_menu_id', $menu->id)
            ->whereNull('parent_id')
            ->with([
                'page',
                'children' => fn ($q) => $q->orderBy('sort_order')->with([
                    'page',
                    'children' => fn ($q2) => $q2->orderBy('sort_order')->with('page'),
                ]),
            ])
            ->orderBy('sort_order')
            ->get();

        $roots = $this->filterNavTreeForPublic($roots);

        return (new NavigationCollection($roots))->additional(['menu_key' => $menu->key]);
    }

    /**
     * @param  Collection<int, NavItem>  $items
     * @return Collection<int, NavItem>
     */
    private function filterNavTreeForPublic(Collection $items): Collection
    {
        return $items
            ->filter(fn (NavItem $item): bool => $item->visibleInPublicApi())
            ->map(function (NavItem $item): NavItem {
                $children = $item->relationLoaded('children')
                    ? $item->children
                    : collect();

                $item->setRelation('children', $this->filterNavTreeForPublic($children));

                return $item;
            })
            ->values();
    }
}
