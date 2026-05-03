<?php

namespace App\Models;

use App\Models\Concerns\HasMorphContentRelations;
use App\Models\Concerns\HasMorphFaqsAndTestimonials;
use App\Services\Content\VideoEmbedResolver;
use App\Support\Slug\UniqueSlugGenerator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class PortfolioItem extends Model
{
    use HasMorphContentRelations;
    use HasMorphFaqsAndTestimonials;
    use SoftDeletes;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_PUBLISHED = 'published';

    protected $fillable = [
        'slug',
        'title',
        'client_name',
        'project_type',
        'industry',
        'short_description',
        'body',
        'challenge',
        'solution',
        'technology_stack',
        'outcome',
        'main_image_media_id',
        'gallery_media_ids',
        'video_url',
        'video_provider',
        'status',
        'featured',
        'sort_order',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical_url',
        'robots',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'technology_stack' => 'array',
            'gallery_media_ids' => 'array',
            'featured' => 'boolean',
            'sort_order' => 'integer',
            'published_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (PortfolioItem $item): void {
            if ($item->slug === null || $item->slug === '') {
                $base = Str::slug($item->title);
                $item->slug = app(UniqueSlugGenerator::class)->unique('portfolio_items', 'slug', $base ?: 'portfolio', null);
            }
        });

        static::saving(function (PortfolioItem $item): void {
            if (($item->slug === null || $item->slug === '') && $item->exists) {
                $base = Str::slug($item->title);
                $item->slug = app(UniqueSlugGenerator::class)->unique('portfolio_items', 'slug', $base ?: 'portfolio', $item->id);
            }

            $resolver = app(VideoEmbedResolver::class);
            $v = $resolver->resolve($item->video_url);
            $item->video_provider = $v['provider'] ?? null;
        });
    }

    /**
     * @param  Builder<PortfolioItem>  $query
     * @return Builder<PortfolioItem>
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('status', self::STATUS_PUBLISHED)
            ->where(function (Builder $q): void {
                $q->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });
    }

    /**
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

    public function refreshUniqueSlug(?string $preferred = null): void
    {
        $base = Str::slug($preferred ?? $this->slug ?? $this->title);
        if ($base === '') {
            $base = 'portfolio';
        }
        $this->slug = app(UniqueSlugGenerator::class)->unique('portfolio_items', 'slug', $base, $this->id);
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
}
