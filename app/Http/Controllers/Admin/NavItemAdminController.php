<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NavItem;
use App\Models\NavMenu;
use App\Models\Page;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class NavItemAdminController extends Controller
{
    public function index(string $nav_menu): View
    {
        $this->authorize('viewAny', NavItem::class);
        $menu = NavMenu::query()->where('key', $nav_menu)->firstOrFail();

        return view('admin.navigation.index', [
            'menu' => $menu,
            'items' => NavItem::query()
                ->where('nav_menu_id', $menu->id)
                ->with(['parent', 'page'])
                ->orderByRaw('parent_id is null desc')
                ->orderBy('parent_id')
                ->orderBy('sort_order')
                ->get(),
        ]);
    }

    public function create(string $nav_menu): View
    {
        $this->authorize('create', NavItem::class);
        $menu = NavMenu::query()->where('key', $nav_menu)->firstOrFail();

        return view('admin.navigation.form', [
            'menu' => $menu,
            'item' => new NavItem(['nav_menu_id' => $menu->id, 'sort_order' => 0]),
            'mode' => 'create',
            'parentOptions' => $this->parentOptions($menu, null),
            'pages' => Page::query()->orderBy('path')->get(['id', 'path', 'title']),
        ]);
    }

    public function store(Request $request, string $nav_menu): JsonResponse|RedirectResponse
    {
        $this->authorize('create', NavItem::class);
        $menu = NavMenu::query()->where('key', $nav_menu)->firstOrFail();
        $data = $this->validated($request, $menu, null);
        NavItem::query()->create($data);

        return $this->respond(
            $request,
            'Navigation item created.',
            route('admin.navigation.index', ['nav_menu' => $menu->key])
        );
    }

    public function edit(string $nav_menu, NavItem $nav_item): View
    {
        $this->authorize('update', $nav_item);
        $menu = NavMenu::query()->where('key', $nav_menu)->firstOrFail();
        abort_unless($nav_item->nav_menu_id === $menu->id, 404);

        return view('admin.navigation.form', [
            'menu' => $menu,
            'item' => $nav_item,
            'mode' => 'edit',
            'parentOptions' => $this->parentOptions($menu, $nav_item),
            'pages' => Page::query()->orderBy('path')->get(['id', 'path', 'title']),
        ]);
    }

    public function update(Request $request, string $nav_menu, NavItem $nav_item): JsonResponse|RedirectResponse
    {
        $this->authorize('update', $nav_item);
        $menu = NavMenu::query()->where('key', $nav_menu)->firstOrFail();
        abort_unless($nav_item->nav_menu_id === $menu->id, 404);
        $nav_item->update($this->validated($request, $menu, $nav_item));

        return $this->respond(
            $request,
            'Navigation item updated.',
            route('admin.navigation.index', ['nav_menu' => $menu->key])
        );
    }

    public function destroy(Request $request, string $nav_menu, NavItem $nav_item): JsonResponse|RedirectResponse
    {
        $this->authorize('delete', $nav_item);
        $menu = NavMenu::query()->where('key', $nav_menu)->firstOrFail();
        abort_unless($nav_item->nav_menu_id === $menu->id, 404);
        $nav_item->delete();

        return $this->respond(
            $request,
            'Navigation item deleted.',
            route('admin.navigation.index', ['nav_menu' => $menu->key])
        );
    }

    /**
     * @return Collection<int, NavItem>
     */
    private function parentOptions(NavMenu $menu, ?NavItem $except): Collection
    {
        return NavItem::query()
            ->where('nav_menu_id', $menu->id)
            ->when($except, fn ($q) => $q->where('id', '!=', $except->id))
            ->orderBy('sort_order')
            ->orderBy('label')
            ->get();
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, NavMenu $menu, ?NavItem $current): array
    {
        $parentRule = [
            'nullable',
            'integer',
            Rule::exists('nav_items', 'id')->where(fn ($q) => $q->where('nav_menu_id', $menu->id)),
        ];

        $data = $request->validate([
            'label' => ['required', 'string', 'max:255'],
            'url' => ['nullable', 'string', 'max:2048'],
            'page_id' => ['nullable', 'integer', 'exists:pages,id'],
            'parent_id' => $parentRule,
            'sort_order' => ['required', 'integer', 'min:0'],
            'feature_payload_json' => ['nullable', 'string', 'max:10000'],
            'visibility_json' => ['nullable', 'string', 'max:10000'],
        ]);

        $payload = null;
        if (! empty(trim($data['feature_payload_json'] ?? ''))) {
            $decoded = json_decode($data['feature_payload_json'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw ValidationException::withMessages(['feature_payload_json' => 'Feature payload must be valid JSON.']);
            }
            $payload = $decoded;
        }

        $visibility = null;
        if (! empty(trim($data['visibility_json'] ?? ''))) {
            $decoded = json_decode($data['visibility_json'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw ValidationException::withMessages(['visibility_json' => 'Visibility must be valid JSON.']);
            }
            $visibility = $decoded;
        }

        $parentId = isset($data['parent_id']) && $data['parent_id'] !== '' ? (int) $data['parent_id'] : null;
        if ($current !== null && $parentId !== null && $parentId === $current->id) {
            throw ValidationException::withMessages(['parent_id' => 'An item cannot be its own parent.']);
        }

        return [
            'nav_menu_id' => $menu->id,
            'label' => $data['label'],
            'url' => $data['url'] ?: null,
            'page_id' => $data['page_id'] ?: null,
            'parent_id' => $parentId,
            'sort_order' => $data['sort_order'],
            'feature_payload' => $payload,
            'visibility' => $visibility,
        ];
    }

    private function respond(Request $request, string $message, string $redirect): JsonResponse|RedirectResponse
    {
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return redirect()->to($redirect)->with('status', $message);
    }
}
