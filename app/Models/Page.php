<?php

namespace App\Models;

use App\Models\Concerns\HasMorphFaqsAndTestimonials;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Page extends Model
{
    use HasMorphFaqsAndTestimonials;

    protected $fillable = [
        'parent_id',
        'path',
        'title',
        'meta_title',
        'meta_description',
        'meta_og_media_id',
        'meta',
        'custom_head_html',
        'custom_body_html',
        'builder_settings_json',
        'status',
        'published_at',
        'archived_at',
        'scheduled_publish_at',
        'primary_entity_type',
        'primary_entity_id',
    ];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
            'builder_settings_json' => 'array',
            'published_at' => 'datetime',
            'archived_at' => 'datetime',
            'scheduled_publish_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Page, $this>
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'parent_id');
    }

    /**
     * @return HasMany<Page, $this>
     */
    public function children(): HasMany
    {
        return $this->hasMany(Page::class, 'parent_id')->orderBy('path');
    }

    /**
     * @return HasMany<PageBlock, $this>
     */
    public function blocks(): HasMany
    {
        return $this->hasMany(PageBlock::class)->orderBy('sort_order');
    }

    /**
     * @return HasMany<PageVersion, $this>
     */
    public function versions(): HasMany
    {
        return $this->hasMany(PageVersion::class)->orderByDesc('id');
    }

    /**
     * @return BelongsTo<MediaAsset, $this>
     */
    public function metaOgMedia(): BelongsTo
    {
        return $this->belongsTo(MediaAsset::class, 'meta_og_media_id');
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function primaryEntity(): MorphTo
    {
        return $this->morphTo('primary_entity');
    }

    /**
     * @param  Builder<Page>  $query
     * @return Builder<Page>
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('status', 'published')
            ->where(function (Builder $q): void {
                $q->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });
    }
}
