<?php

namespace App\Models;

use App\Models\Concerns\HasTerms;
use App\Services\Content\VideoEmbedResolver;
use App\Support\Slug\UniqueSlugGenerator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MarketUpdate extends Model
{
    use HasTerms;
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
        'excerpt',
        'body',
        'market_area',
        'trend_summary',
        'business_impact',
        'technology_impact',
        'opportunities',
        'risks',
        'what_next',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical_url',
        'robots',
        'main_image_path',
        'video_url',
        'video_provider',
        'reading_time_minutes',
        'fact_check_notes',
        'source_requirements',
        'source_urls',
        'status',
        'source_topic',
        'ai_prompt',
        'ai_generation_meta',
        'author_name',
        'author_type',
        'ai_model',
        'slug_locked',
        'review_required',
        'featured',
        'view_count',
        'scheduled_for',
        'published_at',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'source_urls' => 'array',
            'ai_generation_meta' => 'array',
            'featured' => 'boolean',
            'slug_locked' => 'boolean',
            'review_required' => 'boolean',
            'view_count' => 'integer',
            'reading_time_minutes' => 'integer',
            'scheduled_for' => 'datetime',
            'published_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (MarketUpdate $row): void {
            if (($row->slug === null || $row->slug === '') && ! $row->slug_locked) {
                $base = Str::slug($row->title);
                $row->slug = app(UniqueSlugGenerator::class)->unique('market_updates', 'slug', $base ?: 'market-update', null);
            }
        });

        static::saving(function (MarketUpdate $row): void {
            if ($row->isDirty('body')) {
                $row->reading_time_minutes = self::estimateReadingMinutes($row->body);
            }

            if ($row->isDirty('status') && $row->status === self::STATUS_PUBLISHED) {
                $row->slug_locked = true;
            }

            if (($row->slug === null || $row->slug === '') && ! $row->slug_locked && $row->exists) {
                $base = Str::slug($row->title);
                $row->slug = app(UniqueSlugGenerator::class)->unique('market_updates', 'slug', $base ?: 'market-update', $row->id);
            }

            $resolver = app(VideoEmbedResolver::class);
            $v = $resolver->resolve($row->video_url);
            $row->video_provider = $v['provider'] ?? null;
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

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('status', self::STATUS_PUBLISHED)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function scopeByCategorySlug(Builder $query, string $categorySlug): Builder
    {
        return $query->whereHas('terms', function (Builder $q) use ($categorySlug): void {
            $q->where('slug', $categorySlug)
                ->whereHas('taxonomy', fn (Builder $t) => $t->where('slug', 'categories'));
        });
    }

    public function scopeByTagSlug(Builder $query, string $tagSlug): Builder
    {
        return $query->whereHas('terms', function (Builder $q) use ($tagSlug): void {
            $q->where('slug', $tagSlug)
                ->whereHas('taxonomy', fn (Builder $t) => $t->where('slug', 'tags'));
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

    public function getMainImageUrlAttribute(): string
    {
        if (is_string($this->main_image_path) && $this->main_image_path !== '') {
            $disk = config('filesystems.default', 'public');

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

    public function refreshUniqueSlug(?string $preferred = null): void
    {
        if ($this->slug_locked) {
            return;
        }
        $base = Str::slug($preferred ?? $this->slug ?? $this->title);
        if ($base === '') {
            $base = 'market-update';
        }
        $this->slug = app(UniqueSlugGenerator::class)->unique('market_updates', 'slug', $base, $this->id);
    }
}
