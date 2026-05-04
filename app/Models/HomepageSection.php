<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HomepageSection extends Model
{
    protected $fillable = [
        'key',
        'title',
        'subtitle',
        'description',
        'badge_text',
        'button_text',
        'button_url',
        'secondary_button_text',
        'secondary_button_url',
        'image_path',
        'is_enabled',
        'sort_order',
        'settings',
    ];

    protected function casts(): array
    {
        return [
            'is_enabled' => 'boolean',
            'sort_order' => 'integer',
            'settings' => 'array',
        ];
    }

    /**
     * @return HasMany<HomepageSectionItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(HomepageSectionItem::class)->orderBy('sort_order')->orderBy('id');
    }

    /**
     * @return HasMany<HomepageSectionItem, $this>
     */
    public function enabledItems(): HasMany
    {
        return $this->items()->where('is_enabled', true);
    }
}
