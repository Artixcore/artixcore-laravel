<?php

namespace App\Models;

use App\Models\Concerns\HasMorphContentRelations;
use App\Models\Concerns\HasMorphFaqsAndTestimonials;
use App\Models\Concerns\HasTerms;
use App\Services\Content\VideoEmbedResolver;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasMorphContentRelations;
    use HasMorphFaqsAndTestimonials;
    use HasTerms;

    protected $fillable = [
        'slug',
        'title',
        'tagline',
        'platform_type',
        'features',
        'target_audience',
        'pricing_note',
        'use_cases',
        'summary',
        'body',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical_url',
        'robots',
        'video_url',
        'video_provider',
        'main_image_media_id',
        'status',
        'featured',
        'sort_order',
        'view_count',
        'trending_score',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'featured' => 'boolean',
            'sort_order' => 'integer',
            'view_count' => 'integer',
            'trending_score' => 'integer',
            'published_at' => 'datetime',
            'features' => 'array',
            'use_cases' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Product $product): void {
            $resolver = app(VideoEmbedResolver::class);
            $v = $resolver->resolve($product->video_url);
            $product->video_provider = $v['provider'] ?? null;
        });
    }

    /**
     * SaaS / platform marketing hero image (media library table).
     *
     * @return BelongsTo<MediaAsset, $this>
     */
    public function mainImageMedia(): BelongsTo
    {
        return $this->belongsTo(MediaAsset::class, 'main_image_media_id');
    }

    /**
     * @return array{embed_url: string, provider: string}|null
     */
    public function getVideoEmbedAttribute(): ?array
    {
        return app(VideoEmbedResolver::class)->resolve($this->video_url);
    }

    public function getMainImageUrlAttribute(): string
    {
        $url = $this->mainImageMedia?->absoluteUrl();
        if (is_string($url) && $url !== '') {
            return $url;
        }

        $fallback = config('articles.fallback_image_url');

        return is_string($fallback) && $fallback !== '' ? $fallback : asset('theme/images/blog/03.jpg');
    }

    /**
     * @param  Builder<Product>  $query
     * @return Builder<Product>
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
