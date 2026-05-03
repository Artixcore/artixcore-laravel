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

class CaseStudy extends Model
{
    use HasTerms;
    use SoftDeletes;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_PENDING_REVIEW = 'pending_review';

    public const STATUS_SCHEDULED = 'scheduled';

    public const STATUS_PUBLISHED = 'published';

    public const STATUS_ARCHIVED = 'archived';

    public const TYPE_REAL = 'real';

    public const TYPE_ANONYMIZED = 'anonymized';

    public const TYPE_CONCEPT = 'concept';

    public const AUTHOR_TYPE_AI = 'ai';

    public const AUTHOR_TYPE_HUMAN = 'human';

    protected $fillable = [
        'slug',
        'title',
        'client_name',
        'client_verified',
        'client_display_name',
        'summary',
        'body',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical_url',
        'robots',
        'status',
        'case_study_type',
        'industry',
        'project_type',
        'challenge',
        'solution',
        'implementation',
        'technology_stack',
        'outcomes',
        'metrics',
        'lessons_learned',
        'main_image_path',
        'gallery_paths',
        'video_url',
        'video_provider',
        'reading_time_minutes',
        'originality_notes',
        'fact_check_notes',
        'scheduled_for',
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
        'trending_score',
        'published_at',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'client_verified' => 'boolean',
            'featured' => 'boolean',
            'slug_locked' => 'boolean',
            'review_required' => 'boolean',
            'technology_stack' => 'array',
            'outcomes' => 'array',
            'metrics' => 'array',
            'gallery_paths' => 'array',
            'ai_generation_meta' => 'array',
            'view_count' => 'integer',
            'trending_score' => 'integer',
            'published_at' => 'datetime',
            'scheduled_for' => 'datetime',
            'reading_time_minutes' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (CaseStudy $study): void {
            if (($study->slug === null || $study->slug === '') && ! $study->slug_locked) {
                $base = Str::slug($study->title);
                $study->slug = app(UniqueSlugGenerator::class)->unique('case_studies', 'slug', $base ?: 'case-study', null);
            }
        });

        static::saving(function (CaseStudy $study): void {
            if ($study->isDirty(['challenge', 'solution', 'implementation', 'body', 'lessons_learned'])) {
                $study->reading_time_minutes = self::estimateReadingMinutes(collect([
                    $study->challenge,
                    $study->solution,
                    $study->implementation,
                    $study->body,
                    $study->lessons_learned,
                ])->filter()->implode("\n\n"));
            }

            if ($study->isDirty('status') && $study->status === self::STATUS_PUBLISHED) {
                $study->slug_locked = true;
            }

            if (($study->slug === null || $study->slug === '') && ! $study->slug_locked && $study->exists) {
                $base = Str::slug($study->title);
                $study->slug = app(UniqueSlugGenerator::class)->unique('case_studies', 'slug', $base ?: 'case-study', $study->id);
            }

            $resolver = app(VideoEmbedResolver::class);
            $v = $resolver->resolve($study->video_url);
            $study->video_provider = $v['provider'] ?? null;
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

    /**
     * Excerpt maps to legacy `summary` column.
     */
    public function getExcerptAttribute(): ?string
    {
        return $this->summary;
    }

    public function setExcerptAttribute(?string $value): void
    {
        $this->summary = $value;
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('status', self::STATUS_PUBLISHED)
            ->where(function (Builder $q): void {
                $q->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });
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
                ->orWhere('body', 'like', $needle)
                ->orWhere('industry', 'like', $needle)
                ->orWhere('challenge', 'like', $needle);
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

    public function typeLabel(): string
    {
        return match ($this->case_study_type) {
            self::TYPE_REAL => $this->client_verified ? 'Verified client project' : 'Real project',
            self::TYPE_ANONYMIZED => 'Anonymized case study',
            default => 'Concept case study',
        };
    }

    public function getOutcomeSummaryAttribute(): string
    {
        $outcomes = $this->outcomes;
        if (is_array($outcomes) && $outcomes !== []) {
            $parts = [];
            foreach ($outcomes as $o) {
                if (is_string($o)) {
                    $parts[] = $o;
                }
            }
            $flat = implode(' ', $parts);

            return Str::limit(trim(strip_tags($flat)), 220);
        }

        return Str::limit(trim(strip_tags((string) $this->summary)), 220);
    }

    public function refreshUniqueSlug(?string $preferred = null): void
    {
        if ($this->slug_locked) {
            return;
        }
        $base = Str::slug($preferred ?? $this->slug ?? $this->title);
        if ($base === '') {
            $base = 'case-study';
        }
        $this->slug = app(UniqueSlugGenerator::class)->unique('case_studies', 'slug', $base, $this->id);
    }
}
