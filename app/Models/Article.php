<?php

namespace App\Models;

use App\Models\Concerns\HasTerms;
use App\Services\Content\VideoEmbedResolver;
use App\Support\Slug\UniqueSlugGenerator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Article extends Model implements HasMedia
{
    use HasTerms;
    use InteractsWithMedia;
    use SoftDeletes;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_PENDING_REVIEW = 'pending_review';

    public const STATUS_SCHEDULED = 'scheduled';

    public const STATUS_PUBLISHED = 'published';

    public const STATUS_ARCHIVED = 'archived';

    public const AUTHOR_TYPE_AI = 'ai';

    public const AUTHOR_TYPE_HUMAN = 'human';

    protected $fillable = [
        'slug',
        'title',
        'summary',
        'body',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical_url',
        'robots',
        'main_image_path',
        'status',
        'featured',
        'view_count',
        'trending_score',
        'published_at',
        'scheduled_for',
        'author_name',
        'author_type',
        'ai_model',
        'ai_prompt',
        'ai_generation_meta',
        'source_topic',
        'article_type',
        'video_url',
        'video_provider',
        'reading_time_minutes',
        'plagiarism_score',
        'originality_notes',
        'review_required',
        'slug_locked',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'featured' => 'boolean',
            'view_count' => 'integer',
            'trending_score' => 'integer',
            'published_at' => 'datetime',
            'scheduled_for' => 'datetime',
            'ai_generation_meta' => 'array',
            'plagiarism_score' => 'decimal:2',
            'review_required' => 'boolean',
            'slug_locked' => 'boolean',
            'reading_time_minutes' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Article $article): void {
            if (($article->slug === null || $article->slug === '') && ! $article->slug_locked) {
                $base = Str::slug($article->title);
                $article->slug = app(UniqueSlugGenerator::class)->unique('articles', 'slug', $base ?: 'article', null);
            }
        });

        static::saving(function (Article $article): void {
            if ($article->isDirty('body')) {
                $article->reading_time_minutes = self::estimateReadingMinutes($article->body);
            }

            if ($article->isDirty('status') && $article->status === self::STATUS_PUBLISHED) {
                $article->slug_locked = true;
            }

            if (($article->slug === null || $article->slug === '') && ! $article->slug_locked && $article->exists) {
                $base = Str::slug($article->title);
                $article->slug = app(UniqueSlugGenerator::class)->unique('articles', 'slug', $base ?: 'article', $article->id);
            }

            $resolver = app(VideoEmbedResolver::class);
            $v = $resolver->resolve($article->video_url);
            $article->video_provider = $v['provider'] ?? null;
        });
    }

    public static function estimateReadingMinutes(?string $body): int
    {
        $text = trim(preg_replace('/\s+/', ' ', strip_tags((string) $body)) ?? '');
        if ($text === '') {
            return 1;
        }
        $wpm = max(60, (int) config('articles.reading_words_per_minute', 220));
        $words = str_word_count($text);

        return max(1, (int) ceil($words / $wpm));
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('article_main')->singleFile();
        $this->addMediaCollection('article_gallery');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('card')
            ->width(800)
            ->height(450)
            ->nonQueued();
    }

    /**
     * @param  Builder<Article>  $query
     * @return Builder<Article>
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('status', self::STATUS_PUBLISHED)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    /**
     * @param  Builder<Article>  $query
     * @return Builder<Article>
     */
    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    /**
     * @param  Builder<Article>  $query
     * @return Builder<Article>
     */
    public function scopeScheduled(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_SCHEDULED);
    }

    /**
     * @param  Builder<Article>  $query
     * @return Builder<Article>
     */
    public function scopeByCategorySlug(Builder $query, string $categorySlug): Builder
    {
        return $query->whereHas('terms', function (Builder $q) use ($categorySlug): void {
            $q->where('slug', $categorySlug)
                ->whereHas('taxonomy', fn (Builder $t) => $t->where('slug', 'categories'));
        });
    }

    /**
     * @param  Builder<Article>  $query
     * @return Builder<Article>
     */
    public function scopeByTagSlug(Builder $query, string $tagSlug): Builder
    {
        return $query->whereHas('terms', function (Builder $q) use ($tagSlug): void {
            $q->where('slug', $tagSlug)
                ->whereHas('taxonomy', fn (Builder $t) => $t->where('slug', 'tags'));
        });
    }

    /**
     * @param  Builder<Article>  $query
     * @return Builder<Article>
     */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        $term = trim($term);
        if ($term === '') {
            return $query;
        }
        $needle = '%'.str_replace(['%', '_'], ['\\%', '\\_'], $term).'%';

        return $query->where(function (Builder $q) use ($needle): void {
            $q->where('title', 'like', $needle)
                ->orWhere('summary', 'like', $needle)
                ->orWhere('body', 'like', $needle);
        });
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Primary category term (top-level under categories taxonomy).
     */
    public function primaryCategoryTerm(): ?Term
    {
        $terms = $this->relationLoaded('terms')
            ? $this->terms
            : $this->terms()->with('taxonomy', 'parent')->get();

        foreach ($terms as $term) {
            if (($term->taxonomy?->slug ?? '') !== 'categories') {
                continue;
            }
            if ($term->parent_id === null) {
                return $term;
            }
        }

        foreach ($terms as $term) {
            if (($term->taxonomy?->slug ?? '') === 'categories') {
                return $term->parent ?? $term;
            }
        }

        return null;
    }

    /**
     * Subcategory term (child under categories taxonomy).
     */
    public function subcategoryTerm(): ?Term
    {
        $terms = $this->relationLoaded('terms')
            ? $this->terms
            : $this->terms()->with('taxonomy')->get();

        foreach ($terms as $term) {
            if (($term->taxonomy?->slug ?? '') === 'categories' && $term->parent_id !== null) {
                return $term;
            }
        }

        return null;
    }

    /**
     * @return Collection<int, Term>
     */
    public function tagTerms()
    {
        $terms = $this->relationLoaded('terms')
            ? $this->terms
            : $this->terms()->with('taxonomy')->get();

        return $terms->filter(fn (Term $t) => ($t->taxonomy?->slug ?? '') === 'tags')->values();
    }

    public function getMainImageUrlAttribute(): string
    {
        $fromMedia = $this->getFirstMediaUrl('article_main', 'card')
            ?: $this->getFirstMediaUrl('article_main');
        if (is_string($fromMedia) && $fromMedia !== '') {
            return $fromMedia;
        }

        if (is_string($this->main_image_path) && $this->main_image_path !== '') {
            $disk = config('media-library.disk_name', config('filesystems.default', 'public'));

            return Storage::disk($disk)->url($this->main_image_path);
        }

        $fallback = config('articles.fallback_image_url');

        return is_string($fallback) && $fallback !== '' ? $fallback : asset('theme/images/blog/03.jpg');
    }

    /**
     * @return array{embed_url: string, provider: string}|null
     */
    public function getVideoEmbedAttribute(): ?array
    {
        return app(VideoEmbedResolver::class)->resolve($this->video_url);
    }

    /**
     * Ensure stored slug is unique (admin edits).
     */
    public function refreshUniqueSlug(?string $preferred = null): void
    {
        if ($this->slug_locked) {
            return;
        }
        $base = Str::slug($preferred ?? $this->slug ?? $this->title);
        if ($base === '') {
            $base = 'article';
        }
        $this->slug = app(UniqueSlugGenerator::class)->unique('articles', 'slug', $base, $this->id);
    }
}
