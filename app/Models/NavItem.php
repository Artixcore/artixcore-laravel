<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NavItem extends Model
{
    protected $fillable = [
        'nav_menu_id',
        'parent_id',
        'label',
        'url',
        'page_id',
        'sort_order',
        'feature_payload',
        'visibility',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'feature_payload' => 'array',
            'visibility' => 'array',
        ];
    }

    /**
     * Public marketing API / site navigation. Omit `visibility` or include `contexts` containing `public`.
     *
     * @param  array<string, mixed>|null  $visibility
     */
    public static function isVisibleInPublicApi(?array $visibility): bool
    {
        if ($visibility === null || $visibility === []) {
            return true;
        }

        $contexts = $visibility['contexts'] ?? null;
        if (! is_array($contexts) || $contexts === []) {
            return true;
        }

        return in_array('public', $contexts, true);
    }

    public function visibleInPublicApi(): bool
    {
        return self::isVisibleInPublicApi($this->visibility);
    }

    /**
     * @return BelongsTo<NavMenu, $this>
     */
    public function menu(): BelongsTo
    {
        return $this->belongsTo(NavMenu::class, 'nav_menu_id');
    }

    /**
     * @return BelongsTo<NavItem, $this>
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(NavItem::class, 'parent_id');
    }

    /**
     * @return HasMany<NavItem, $this>
     */
    public function children(): HasMany
    {
        return $this->hasMany(NavItem::class, 'parent_id')->orderBy('sort_order');
    }

    /**
     * @return BelongsTo<Page, $this>
     */
    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function resolvedPath(): ?string
    {
        if ($this->url) {
            return $this->url;
        }
        if ($this->page && $this->page->path) {
            $path = $this->page->path;

            return $path === 'home' ? '/' : '/'.$path;
        }

        return null;
    }
}
