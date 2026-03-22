<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\NavigationCollection;
use App\Models\NavItem;
use App\Models\NavMenu;
use Illuminate\Http\Request;

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

        return (new NavigationCollection($roots))->additional(['menu_key' => $menu->key]);
    }
}
