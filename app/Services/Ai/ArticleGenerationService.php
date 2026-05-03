<?php

namespace App\Services\Ai;

use App\Models\AiProvider;
use App\Models\Article;
use App\Models\Taxonomy;
use App\Models\Term;
use App\Services\Ai\Clients\OpenAiCompatibleClient;
use App\Services\Ai\Exceptions\LlmTransportException;
use App\Services\HtmlSanitizer;
use App\Support\Slug\UniqueSlugGenerator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class ArticleGenerationService
{
    public function __construct(
        private OpenAiCompatibleClient $client,
        private HtmlSanitizer $sanitizer,
        private UniqueSlugGenerator $slugGenerator,
    ) {}

    /**
     * Ephemeral provider from OPENAI_API_KEY or first enabled DB provider.
     */
    public function resolveProvider(): ?AiProvider
    {
        $key = config('ai_articles.openai_api_key');
        if (is_string($key) && $key !== '') {
            $provider = new AiProvider;
            $provider->forceFill([
                'driver' => AiProvider::DRIVER_OPENAI,
                'name' => 'Article generator (env)',
                'is_enabled' => true,
                'api_key_encrypted' => $key,
                'base_url' => config('ai.default_openai_base'),
                'default_model' => config('ai_articles.model'),
                'timeout_seconds' => 120,
                'max_output_tokens' => 4096,
                'metadata' => ['temperature' => 0.55],
            ]);

            return $provider;
        }

        return AiProvider::query()
            ->where('is_enabled', true)
            ->orderBy('priority')
            ->get()
            ->first(fn (AiProvider $p): bool => $p->hasApiKey());
    }

    public function providerConfigured(): bool
    {
        return $this->resolveProvider() !== null;
    }

    public function countAiArticlesCreatedToday(): int
    {
        return Article::query()
            ->where('author_type', Article::AUTHOR_TYPE_AI)
            ->whereDate('created_at', today())
            ->count();
    }

    public function remainingDailySlots(): int
    {
        $limit = (int) config('ai_articles.daily_limit', 3);

        return max(0, $limit - $this->countAiArticlesCreatedToday());
    }

    /**
     * @return array<string, mixed>
     */
    public function generateStructuredPayload(
        string $articleType,
        ?string $adminTopic = null,
    ): array {
        $provider = $this->resolveProvider();
        if ($provider === null) {
            throw new LlmTransportException('No AI provider API key configured.');
        }

        $model = is_string($provider->default_model) && $provider->default_model !== ''
            ? $provider->default_model
            : (string) config('ai_articles.model');

        $messages = [
            ['role' => 'system', 'content' => $this->systemPrompt()],
            ['role' => 'user', 'content' => $this->userPrompt($articleType, $adminTopic)],
        ];

        $result = $this->client->complete($provider, $messages, $model, min(8192, (int) ($provider->max_output_tokens ?? 4096)));

        return $this->parseJsonPayload($result->content);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function createArticleFromPayload(array $payload, string $articleType, ?string $sourceTopic = null): Article
    {
        $author = (string) config('ai_articles.author_name', 'Ali 1.0');

        $title = (string) ($payload['title'] ?? 'Untitled draft');
        $excerpt = (string) ($payload['excerpt'] ?? '');
        $bodyRaw = (string) ($payload['body'] ?? '');
        $body = $this->sanitizer->sanitize($bodyRaw);

        $metaTitle = Str::limit((string) ($payload['meta_title'] ?? $title), 60);
        $metaDesc = Str::limit((string) ($payload['meta_description'] ?? $excerpt), 160);
        $keywords = (string) ($payload['meta_keywords'] ?? '');

        $suggestedSlug = (string) ($payload['suggested_slug'] ?? '');
        $slugBase = $suggestedSlug !== '' ? Str::slug($suggestedSlug) : Str::slug($title);
        $slug = $this->slugGenerator->unique('articles', 'slug', $slugBase ?: 'article', null);

        $originalityNotes = (string) ($payload['originality_notes'] ?? '');
        $factCheckNotes = (string) ($payload['fact_check_notes'] ?? '');
        $readingMinutes = isset($payload['reading_time_minutes']) ? (int) $payload['reading_time_minutes'] : Article::estimateReadingMinutes($body);

        $status = config('ai_articles.auto_publish', false) ? Article::STATUS_PUBLISHED : Article::STATUS_PENDING_REVIEW;
        $publishedAt = $status === Article::STATUS_PUBLISHED ? now() : null;

        $article = Article::query()->create([
            'slug' => $slug,
            'title' => Str::limit($title, 255),
            'summary' => Str::limit($excerpt, 500),
            'body' => $body,
            'meta_title' => Str::limit($metaTitle, 255),
            'meta_description' => $metaDesc,
            'meta_keywords' => Str::limit($keywords, 255),
            'status' => $status,
            'featured' => false,
            'published_at' => $publishedAt,
            'author_name' => $author,
            'author_type' => Article::AUTHOR_TYPE_AI,
            'ai_model' => config('ai_articles.model'),
            'ai_prompt' => $this->userPrompt($articleType, $sourceTopic),
            'ai_generation_meta' => [
                'fact_check_notes' => $factCheckNotes,
                'regenerated_at' => now()->toIso8601String(),
            ],
            'source_topic' => $sourceTopic ?? (string) ($payload['source_topic'] ?? ''),
            'article_type' => $payload['article_type'] ?? $articleType,
            'review_required' => true,
            'originality_notes' => $originalityNotes !== '' ? $originalityNotes : null,
            'reading_time_minutes' => max(1, $readingMinutes),
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        if ($status === Article::STATUS_PUBLISHED) {
            $article->slug_locked = true;
            $article->save();
        }

        $this->syncTermsFromPayload($article, $payload);

        return $article->fresh(['terms.taxonomy']);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function applyPayloadToArticle(Article $article, array $payload, bool $replaceBody): void
    {
        if (isset($payload['title'])) {
            $article->title = Str::limit((string) $payload['title'], 255);
        }
        if (isset($payload['excerpt'])) {
            $article->summary = Str::limit((string) $payload['excerpt'], 500);
        }
        if ($replaceBody && isset($payload['body'])) {
            $article->body = $this->sanitizer->sanitize((string) $payload['body']);
        }
        if (isset($payload['meta_title'])) {
            $article->meta_title = Str::limit((string) $payload['meta_title'], 60);
        }
        if (isset($payload['meta_description'])) {
            $article->meta_description = Str::limit((string) $payload['meta_description'], 160);
        }
        if (isset($payload['meta_keywords'])) {
            $article->meta_keywords = Str::limit((string) $payload['meta_keywords'], 255);
        }
        if (isset($payload['originality_notes'])) {
            $article->originality_notes = (string) $payload['originality_notes'];
        }
        if (isset($payload['suggested_slug']) && ! $article->slug_locked) {
            $base = Str::slug((string) $payload['suggested_slug']);
            if ($base !== '') {
                $article->slug = $this->slugGenerator->unique('articles', 'slug', $base, $article->id);
            }
        }

        $article->reading_time_minutes = Article::estimateReadingMinutes($article->body);
        $article->updated_by = Auth::id();
        $article->save();

        $this->syncTermsFromPayload($article, $payload);
    }

    /**
     * Regenerate title, excerpt, meta only (keeps body unless empty).
     *
     * @return array<string, mixed>
     */
    public function regenerateMeta(Article $article): array
    {
        $payload = $this->generateStructuredPayload(
            (string) ($article->article_type ?? 'latest_tech'),
            $article->source_topic
        );
        $this->applyPayloadToArticle($article->fresh(), $payload, replaceBody: false);

        return $payload;
    }

    /**
     * @return array<string, mixed>
     */
    public function regenerateBody(Article $article): array
    {
        $payload = $this->generateStructuredPayload(
            (string) ($article->article_type ?? 'latest_tech'),
            $article->source_topic
        );
        $this->applyPayloadToArticle($article->fresh(), $payload, replaceBody: true);

        return $payload;
    }

    private function systemPrompt(): string
    {
        return <<<'PROMPT'
You are "Ali 1.0", Artixcore's editorial assistant. Respond with a single JSON object only (no markdown fences).
Audience: SaaS founders, startup teams, enterprise engineering leaders, developers, AI/software buyers, business owners.
Tone: professional, premium, technically grounded but accessible.

Hard rules:
- Write original synthesis and commentary. Do not copy or closely paraphrase sources you might know.
- Never invent breaking news, statistics, quotes, product releases, or dates. For "latest discovery" or cutting-edge tech angles, write evergreen trend analysis, frameworks, or educational guidance with explicit uncertainty ("may", "often", "typically") and add a short disclaimer that facts must be verified before publishing.
- Avoid sensational claims. Include brief fact_check_notes listing what should be verified externally.
- Body may use simple HTML only: p, br, strong, em, ul, ol, li, a (href http/https only), h2, h3, h4, blockquote, code, pre.
- suggested_slug: short kebab-case derived from title.

JSON keys (all strings unless noted):
title, excerpt, body (HTML string), meta_title (<=60 chars), meta_description (140-160 chars), meta_keywords (comma-separated),
category (taxonomy term name under categories), subcategory (optional child name), tags (array of short tag strings),
suggested_slug, article_type (string), source_topic (string), image_prompt (string),
video_search_hint (optional string), originality_notes (string), fact_check_notes (string), reading_time_minutes (integer estimate).
PROMPT;
    }

    private function userPrompt(string $articleType, ?string $topic): string
    {
        $bucketGuide = match ($articleType) {
            'latest_discovery' => 'Bucket: latest discovery / innovation — educational synthesis and cautious framing, no fabricated announcements.',
            'today_trends' => 'Bucket: today\'s trends / digital business — balanced trend analysis with frameworks teams can apply.',
            'latest_tech' => 'Bucket: latest technology (AI, SaaS, security, cloud, Laravel/React ecosystem) — architecture and decision-making guidance.',
            default => 'Bucket: general insight for Artixcore readers.',
        };

        $topicLine = $topic ? "Preferred angle / topic hint from editor: {$topic}" : 'Pick a fresh angle within the bucket without repeating prior clichés.';

        return $bucketGuide."\n".$topicLine."\nArticle type field must be: {$articleType}";
    }

    /**
     * @return array<string, mixed>
     */
    private function parseJsonPayload(string $raw): array
    {
        $text = trim($raw);
        $text = preg_replace('/^```(?:json)?\s*/i', '', $text) ?? $text;
        $text = preg_replace('/\s*```$/', '', $text) ?? $text;

        $decoded = json_decode($text, true);
        if (! is_array($decoded)) {
            throw new LlmTransportException('Article AI response was not valid JSON.');
        }

        return $decoded;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function syncTermsFromPayload(Article $article, array $payload): void
    {
        $ids = [];

        $categoryTax = Taxonomy::query()->firstOrCreate(['slug' => 'categories'], ['name' => 'Categories']);
        $tagsTax = Taxonomy::query()->firstOrCreate(['slug' => 'tags'], ['name' => 'Tags']);

        $parentCategory = null;
        $catName = trim((string) ($payload['category'] ?? ''));
        if ($catName !== '') {
            $parentCategory = Term::query()->firstOrCreate(
                ['taxonomy_id' => $categoryTax->id, 'slug' => Str::slug($catName)],
                ['name' => $catName, 'sort_order' => 0, 'parent_id' => null]
            );
            $ids[] = $parentCategory->id;
        }

        $subName = trim((string) ($payload['subcategory'] ?? ''));
        if ($subName !== '' && $parentCategory !== null) {
            $child = Term::query()->updateOrCreate(
                [
                    'taxonomy_id' => $categoryTax->id,
                    'parent_id' => $parentCategory->id,
                    'slug' => Str::slug($subName),
                ],
                ['name' => $subName, 'sort_order' => 0]
            );
            $ids[] = $child->id;
        }

        $tags = $payload['tags'] ?? [];
        if (is_array($tags)) {
            foreach ($tags as $tag) {
                $name = trim((string) $tag);
                if ($name === '') {
                    continue;
                }
                $term = Term::query()->firstOrCreate(
                    ['taxonomy_id' => $tagsTax->id, 'slug' => Str::slug($name)],
                    ['name' => $name, 'sort_order' => 0, 'parent_id' => null]
                );
                $ids[] = $term->id;
            }
        }

        $ids = array_values(array_unique(array_filter($ids)));
        if ($ids !== []) {
            $article->terms()->sync($ids);
        }
    }

    /**
     * Automated daily generation of one draft from bucket rotation.
     */
    public function generateScheduledDraft(?string $forcedType = null, ?string $forcedTopic = null): ?Article
    {
        $provider = $this->resolveProvider();
        if ($provider === null) {
            Log::info('articles.ai.skip_no_provider');

            return null;
        }

        $remaining = $this->remainingDailySlots();
        if ($remaining <= 0) {
            Log::info('articles.ai.skip_daily_cap');

            return null;
        }

        $buckets = config('ai_articles.content_buckets', ['latest_discovery', 'today_trends', 'latest_tech']);
        $idx = (int) Cache::get('articles:ai:last_bucket_idx', -1);
        $idx = ($idx + 1) % max(1, count($buckets));
        Cache::forever('articles:ai:last_bucket_idx', $idx);

        $type = $forcedType ?? ($buckets[$idx] ?? 'latest_tech');

        try {
            $payload = $this->generateStructuredPayload($type, $forcedTopic);
            $topicKey = (string) ($payload['source_topic'] ?? $payload['title'] ?? '');
            if ($topicKey !== '' && Article::query()->where('source_topic', $topicKey)->exists()) {
                $payload['title'] = ($payload['title'] ?? 'Article').' ('.now()->format('Y-m-d').')';
            }

            return $this->createArticleFromPayload($payload, $type, $forcedTopic);
        } catch (Throwable $e) {
            Log::warning('articles.ai.generation_failed', ['message' => $e->getMessage()]);
            throw $e;
        }
    }
}
