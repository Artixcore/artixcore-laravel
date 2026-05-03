<?php

namespace App\Services\Ai;

use App\Models\MarketUpdate;
use App\Services\Ai\Clients\OpenAiCompatibleClient;
use App\Services\Ai\Exceptions\LlmTransportException;
use App\Services\HtmlSanitizer;
use App\Support\Ai\AiPayloadTermSynchronizer;
use App\Support\Slug\UniqueSlugGenerator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class MarketUpdateGenerationService
{
    public function __construct(
        private OpenAiCompatibleClient $client,
        private HtmlSanitizer $sanitizer,
        private UniqueSlugGenerator $slugGenerator,
        private ArticleGenerationService $providerLocator,
    ) {}

    public function providerConfigured(): bool
    {
        return $this->providerLocator->providerConfigured();
    }

    public function countAiMarketUpdatesCreatedToday(): int
    {
        return MarketUpdate::query()
            ->where('author_type', MarketUpdate::AUTHOR_TYPE_AI)
            ->whereDate('created_at', today())
            ->count();
    }

    public function remainingDailySlots(): int
    {
        $limit = (int) config('ai_content.market_update.daily_limit', 1);

        return max(0, $limit - $this->countAiMarketUpdatesCreatedToday());
    }

    public function daysSinceLastAiMarketUpdate(): ?int
    {
        $last = MarketUpdate::query()
            ->where('author_type', MarketUpdate::AUTHOR_TYPE_AI)
            ->orderByDesc('id')
            ->first();

        return $last ? (int) floor($last->created_at->diffInDays(now())) : null;
    }

    public function shouldRunScheduledGeneration(bool $force): bool
    {
        if (! config('ai_content.market_update.enabled', true)) {
            return false;
        }

        if ($force) {
            return true;
        }

        $interval = (int) config('ai_content.market_update.interval_days', 2);
        $days = $this->daysSinceLastAiMarketUpdate();

        if ($days === null) {
            return true;
        }

        return $days >= $interval;
    }

    /**
     * @return array<string, mixed>
     */
    public function generateStructuredPayload(?string $adminTopic = null): array
    {
        $provider = $this->providerLocator->resolveProvider();
        if ($provider === null) {
            throw new LlmTransportException('No AI provider API key configured.');
        }

        $model = is_string($provider->default_model) && $provider->default_model !== ''
            ? $provider->default_model
            : (string) config('ai_articles.model');

        $topic = $this->resolveAreaHint($adminTopic);

        $messages = [
            ['role' => 'system', 'content' => $this->systemPrompt()],
            ['role' => 'user', 'content' => $this->userPrompt($topic)],
        ];

        $result = $this->client->complete($provider, $messages, $model, min(8192, (int) ($provider->max_output_tokens ?? 4096)));

        $payload = $this->parseJsonPayload($result->content);
        if (trim((string) ($payload['source_topic'] ?? '')) === '') {
            $payload['source_topic'] = $topic;
        }

        return $payload;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function createMarketUpdateFromPayload(array $payload, ?string $sourceTopic = null): MarketUpdate
    {
        $author = (string) config('ai_content.author_name', 'Ali 1.0');

        $title = (string) ($payload['title'] ?? 'Market trends draft');
        $excerpt = Str::limit((string) ($payload['excerpt'] ?? ''), 500);

        $trendSummary = $this->sanitizer->sanitize((string) ($payload['trend_summary'] ?? ''));
        $businessImpact = $this->sanitizer->sanitize((string) ($payload['business_impact'] ?? ''));
        $technologyImpact = $this->sanitizer->sanitize((string) ($payload['technology_impact'] ?? ''));
        $opportunities = $this->sanitizer->sanitize((string) ($payload['opportunities'] ?? ''));
        $risks = $this->sanitizer->sanitize((string) ($payload['risks'] ?? ''));
        $whatNext = $this->sanitizer->sanitize((string) ($payload['what_businesses_should_do_next'] ?? ($payload['what_next'] ?? '')));

        $bodyExtra = $this->sanitizer->sanitize((string) ($payload['body'] ?? ''));
        $body = $this->composeBody($trendSummary, $businessImpact, $technologyImpact, $opportunities, $risks, $whatNext, $bodyExtra);

        $metaTitle = Str::limit((string) ($payload['meta_title'] ?? $title), 255);
        $metaDesc = Str::limit((string) ($payload['meta_description'] ?? $excerpt), 500);
        $keywords = Str::limit((string) ($payload['meta_keywords'] ?? ''), 255);

        $suggestedSlug = (string) ($payload['suggested_slug'] ?? '');
        $slugBase = $suggestedSlug !== '' ? Str::slug($suggestedSlug) : Str::slug($title);
        $slug = $this->slugGenerator->unique('market_updates', 'slug', $slugBase ?: 'market-update', null);

        $factCheckNotes = (string) ($payload['fact_check_notes'] ?? '');
        $sourceRequirements = (string) ($payload['source_requirements'] ?? '');
        $readingMinutes = isset($payload['reading_time_minutes']) ? (int) $payload['reading_time_minutes'] : MarketUpdate::estimateReadingMinutes($body);

        $autoPublish = (bool) config('ai_content.market_update.auto_publish', false);
        $status = $autoPublish ? MarketUpdate::STATUS_PUBLISHED : MarketUpdate::STATUS_PENDING_REVIEW;
        $publishedAt = $status === MarketUpdate::STATUS_PUBLISHED ? now() : null;

        $row = MarketUpdate::query()->create([
            'slug' => $slug,
            'title' => Str::limit($title, 255),
            'excerpt' => $excerpt !== '' ? $excerpt : null,
            'body' => $body,
            'market_area' => Str::limit((string) ($payload['market_area'] ?? ''), 255),
            'trend_summary' => $trendSummary !== '' ? $trendSummary : null,
            'business_impact' => $businessImpact !== '' ? $businessImpact : null,
            'technology_impact' => $technologyImpact !== '' ? $technologyImpact : null,
            'opportunities' => $opportunities !== '' ? $opportunities : null,
            'risks' => $risks !== '' ? $risks : null,
            'what_next' => $whatNext !== '' ? $whatNext : null,
            'meta_title' => $metaTitle,
            'meta_description' => $metaDesc,
            'meta_keywords' => $keywords !== '' ? $keywords : null,
            'status' => $status,
            'featured' => false,
            'published_at' => $publishedAt,
            'author_name' => $author,
            'author_type' => MarketUpdate::AUTHOR_TYPE_AI,
            'ai_model' => config('ai_articles.model'),
            'ai_prompt' => $this->userPrompt((string) ($payload['source_topic'] ?? $sourceTopic ?? '')),
            'ai_generation_meta' => [
                'fact_check_notes' => $factCheckNotes,
                'source_requirements' => $sourceRequirements,
                'regenerated_at' => now()->toIso8601String(),
            ],
            'source_topic' => $sourceTopic ?? (string) ($payload['source_topic'] ?? ''),
            'fact_check_notes' => $factCheckNotes !== '' ? $factCheckNotes : null,
            'source_requirements' => $sourceRequirements !== '' ? $sourceRequirements : null,
            'source_urls' => null,
            'review_required' => true,
            'reading_time_minutes' => max(1, $readingMinutes),
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        if ($status === MarketUpdate::STATUS_PUBLISHED) {
            $row->slug_locked = true;
            $row->save();
        }

        AiPayloadTermSynchronizer::sync($row, $payload);

        return $row->fresh(['terms.taxonomy']);
    }

    public function generateScheduledDraft(?string $forcedTopic = null): ?MarketUpdate
    {
        if (! $this->providerConfigured()) {
            Log::info('market_updates.ai.skip_no_provider');

            return null;
        }

        if ($this->remainingDailySlots() <= 0) {
            Log::info('market_updates.ai.skip_daily_cap');

            return null;
        }

        try {
            $payload = $this->generateStructuredPayload($forcedTopic);

            return $this->createMarketUpdateFromPayload($payload, $forcedTopic);
        } catch (Throwable $e) {
            Log::warning('market_updates.ai.generation_failed', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    private function systemPrompt(): string
    {
        return <<<'PROMPT'
You are "Ali 1.0", Artixcore's editorial assistant. Respond with a single JSON object only (no markdown fences).

Hard rules — no live news feed is attached:
- Do not invent statistics, funding rounds, revenue figures, market sizes, dates of announcements, company statements, or scientific breakthroughs.
- Do not name specific companies, products, or research labs unless they are generic archetypes ("a large SaaS vendor") — prefer anonymous descriptions.
- Write qualitative trend analysis using cautious language: "industry signals suggest", "many teams are exploring", "patterns often include".
- Explicitly tell editors to verify any numbers before publishing via fact_check_notes and source_requirements.
- Body and section fields may use simple HTML: p, br, strong, em, ul, ol, li, a (href http/https only), h2, h3, blockquote.

JSON keys:
title, excerpt, body (optional HTML narrative tying sections together),
market_area, trend_summary, business_impact, technology_impact, opportunities, risks,
what_businesses_should_do_next,
meta_title, meta_description, meta_keywords, category, subcategory, tags (array),
suggested_slug, source_topic, fact_check_notes, source_requirements, reading_time_minutes (integer).

what_businesses_should_do_next should be practical non-prescriptive guidance ("consider evaluating…", "review governance…").
PROMPT;
    }

    private function userPrompt(string $area): string
    {
        return 'Produce an editorial market/industry update draft focused on this area (qualitative only): '.$area;
    }

    private function resolveAreaHint(?string $adminTopic): string
    {
        $t = trim((string) $adminTopic);
        if ($t !== '') {
            return $t;
        }
        $pool = config('ai_content.market_update_areas', []);

        return is_array($pool) && $pool !== [] ? (string) $pool[array_rand($pool)] : 'Enterprise software buyer trends';
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
            throw new LlmTransportException('Market update AI response was not valid JSON.');
        }

        return $decoded;
    }

    private function composeBody(
        string $trendSummary,
        string $businessImpact,
        string $technologyImpact,
        string $opportunities,
        string $risks,
        string $whatNext,
        string $bodyExtra,
    ): string {
        $blocks = [];
        if ($trendSummary !== '') {
            $blocks[] = '<h2>Trend summary</h2>'.$trendSummary;
        }
        if ($businessImpact !== '') {
            $blocks[] = '<h2>Business impact</h2>'.$businessImpact;
        }
        if ($technologyImpact !== '') {
            $blocks[] = '<h2>Technology impact</h2>'.$technologyImpact;
        }
        if ($opportunities !== '') {
            $blocks[] = '<h2>Opportunities</h2>'.$opportunities;
        }
        if ($risks !== '') {
            $blocks[] = '<h2>Risks</h2>'.$risks;
        }
        if ($whatNext !== '') {
            $blocks[] = '<h2>What businesses should explore next</h2>'.$whatNext;
        }
        $composed = implode("\n", $blocks);
        if ($bodyExtra !== '') {
            $composed .= "\n".$bodyExtra;
        }

        return $composed;
    }
}
