<?php

namespace App\Models;

use App\Models\Concerns\HasMorphContentRelations;
use App\Models\Concerns\HasMorphFaqsAndTestimonials;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Service extends Model
{
    use HasMorphContentRelations;
    use HasMorphFaqsAndTestimonials;

    protected $fillable = [
        'slug',
        'title',
        'summary',
        'body',
        'benefits',
        'process',
        'technologies',
        'icon',
        'featured_image_media_id',
        'featured',
        'sort_order',
        'status',
        'published_at',
        'meta',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical_url',
        'robots',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'published_at' => 'datetime',
            'meta' => 'array',
            'benefits' => 'array',
            'process' => 'array',
            'technologies' => 'array',
            'featured' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<MediaAsset, $this>
     */
    public function featuredImageMedia(): BelongsTo
    {
        return $this->belongsTo(MediaAsset::class, 'featured_image_media_id');
    }

    public function getMainImageUrlAttribute(): string
    {
        $url = $this->featuredImageMedia?->absoluteUrl();
        if (is_string($url) && $url !== '') {
            return $url;
        }

        $fallback = config('articles.fallback_image_url');

        return is_string($fallback) && $fallback !== '' ? $fallback : asset('theme/images/blog/03.jpg');
    }

    /**
     * @param  Builder<Service>  $query
     * @return Builder<Service>
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
