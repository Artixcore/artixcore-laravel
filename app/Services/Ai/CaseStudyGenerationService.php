<?php

namespace App\Services\Ai;

use App\Models\CaseStudy;
use App\Services\Ai\Clients\OpenAiCompatibleClient;
use App\Services\Ai\Exceptions\LlmTransportException;
use App\Services\HtmlSanitizer;
use App\Support\Ai\AiPayloadTermSynchronizer;
use App\Support\Slug\UniqueSlugGenerator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class CaseStudyGenerationService
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

    public function countAiCaseStudiesCreatedToday(): int
    {
        return CaseStudy::query()
            ->where('author_type', CaseStudy::AUTHOR_TYPE_AI)
            ->whereDate('created_at', today())
            ->count();
    }

    public function remainingDailySlots(): int
    {
        $limit = (int) config('ai_content.case_study.daily_limit', 1);

        return max(0, $limit - $this->countAiCaseStudiesCreatedToday());
    }

    /**
     * Minimum whole days since the most recent AI case study was created.
     */
    public function daysSinceLastAiCaseStudy(): ?int
    {
        $last = CaseStudy::query()
            ->where('author_type', CaseStudy::AUTHOR_TYPE_AI)
            ->orderByDesc('id')
            ->first();

        return $last ? (int) floor($last->created_at->diffInDays(now())) : null;
    }

    public function shouldRunScheduledGeneration(bool $force): bool
    {
        if (! config('ai_content.case_study.enabled', true)) {
            return false;
        }

        if ($force) {
            return true;
        }

        $interval = (int) config('ai_content.case_study.interval_days', 2);
        $days = $this->daysSinceLastAiCaseStudy();

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

        $topic = $this->resolveTopicHint($adminTopic);

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
    public function createCaseStudyFromPayload(array $payload, ?string $sourceTopic = null): CaseStudy
    {
        $author = (string) config('ai_content.author_name', 'Ali 1.0');

        $title = (string) ($payload['title'] ?? 'Untitled concept case study');
        $excerpt = Str::limit((string) ($payload['excerpt'] ?? ''), 500);

        $challenge = $this->sanitizer->sanitize((string) ($payload['challenge'] ?? ''));
        $solution = $this->sanitizer->sanitize((string) ($payload['solution'] ?? ''));
        $implementation = $this->sanitizer->sanitize((string) ($payload['implementation'] ?? ''));
        $lessons = $this->sanitizer->sanitize((string) ($payload['lessons_learned'] ?? ''));

        $body = $this->composeStructuredBody($challenge, $solution, $implementation, $lessons);

        $techStack = $this->normalizeStringList($payload['technology_stack'] ?? []);
        $outcomes = $this->normalizeStringList($payload['outcomes'] ?? []);
        $metrics = $this->normalizeMetrics($payload['metrics'] ?? []);

        $metaTitle = Str::limit((string) ($payload['meta_title'] ?? $title), 255);
        $metaDesc = Str::limit((string) ($payload['meta_description'] ?? $excerpt), 500);
        $keywords = Str::limit((string) ($payload['meta_keywords'] ?? ''), 255);

        $suggestedSlug = (string) ($payload['suggested_slug'] ?? '');
        $slugBase = $suggestedSlug !== '' ? Str::slug($suggestedSlug) : Str::slug($title);
        $slug = $this->slugGenerator->unique('case_studies', 'slug', $slugBase ?: 'case-study', null);

        $originalityNotes = (string) ($payload['originality_notes'] ?? '');
        $factCheckNotes = (string) ($payload['fact_check_notes'] ?? '');
        $readingMinutes = isset($payload['reading_time_minutes']) ? (int) $payload['reading_time_minutes'] : CaseStudy::estimateReadingMinutes($body);

        $autoPublish = (bool) config('ai_content.case_study.auto_publish', false);
        $status = $autoPublish ? CaseStudy::STATUS_PUBLISHED : CaseStudy::STATUS_PENDING_REVIEW;
        $publishedAt = $status === CaseStudy::STATUS_PUBLISHED ? now() : null;

        $study = CaseStudy::query()->create([
            'slug' => $slug,
            'title' => Str::limit($title, 255),
            'summary' => $excerpt !== '' ? $excerpt : null,
            'body' => $body,
            'challenge' => $challenge !== '' ? $challenge : null,
            'solution' => $solution !== '' ? $solution : null,
            'implementation' => $implementation !== '' ? $implementation : null,
            'lessons_learned' => $lessons !== '' ? $lessons : null,
            'technology_stack' => $techStack !== [] ? $techStack : null,
            'outcomes' => $outcomes !== [] ? $outcomes : null,
            'metrics' => $metrics !== [] ? $metrics : null,
            'meta_title' => $metaTitle,
            'meta_description' => $metaDesc,
            'meta_keywords' => $keywords !== '' ? $keywords : null,
            'status' => $status,
            'case_study_type' => CaseStudy::TYPE_CONCEPT,
            'client_verified' => false,
            'client_name' => null,
            'client_display_name' => Str::limit((string) ($payload['client_display_name'] ?? 'Representative organization (illustrative)'), 255),
            'industry' => Str::limit((string) ($payload['industry'] ?? ''), 255),
            'project_type' => Str::limit((string) ($payload['project_type'] ?? ''), 255),
            'featured' => false,
            'published_at' => $publishedAt,
            'author_name' => $author,
            'author_type' => CaseStudy::AUTHOR_TYPE_AI,
            'ai_model' => config('ai_articles.model'),
            'ai_prompt' => $this->userPrompt((string) ($payload['source_topic'] ?? $sourceTopic ?? '')),
            'ai_generation_meta' => [
                'image_prompt' => (string) ($payload['image_prompt'] ?? ''),
                'video_search_hint' => (string) ($payload['video_search_hint'] ?? ''),
                'fact_check_notes' => $factCheckNotes,
                'regenerated_at' => now()->toIso8601String(),
            ],
            'source_topic' => $sourceTopic ?? (string) ($payload['source_topic'] ?? ''),
            'review_required' => true,
            'originality_notes' => $originalityNotes !== '' ? $originalityNotes : null,
            'fact_check_notes' => $factCheckNotes !== '' ? $factCheckNotes : null,
            'reading_time_minutes' => max(1, $readingMinutes),
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        if ($status === CaseStudy::STATUS_PUBLISHED) {
            $study->slug_locked = true;
            $study->save();
        }

        AiPayloadTermSynchronizer::sync($study, $payload);

        return $study->fresh(['terms.taxonomy']);
    }

    public function generateScheduledDraft(?string $forcedTopic = null): ?CaseStudy
    {
        if (! $this->providerConfigured()) {
            Log::info('case_studies.ai.skip_no_provider');

            return null;
        }

        if ($this->remainingDailySlots() <= 0) {
            Log::info('case_studies.ai.skip_daily_cap');

            return null;
        }

        try {
            $payload = $this->generateStructuredPayload($forcedTopic);

            return $this->createCaseStudyFromPayload($payload, $forcedTopic);
        } catch (Throwable $e) {
            Log::warning('case_studies.ai.generation_failed', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    private function systemPrompt(): string
    {
        return <<<'PROMPT'
You are "Ali 1.0", Artixcore's editorial assistant. Respond with a single JSON object only (no markdown fences).
Audience: founders, businesses, SaaS buyers, enterprise teams, tech leaders, developers, AI/cloud/security decision-makers.

Hard rules for automated drafts:
- This must be a concept / illustrative case study only. Set case_study_type JSON field to "concept" always.
- Never claim a real client engagement with Artixcore, never name real companies as clients, never cite verifiable results as factual.
- Never invent metrics as empirical facts. For metrics use qualitative framing ("teams often report…") or explicitly labelled illustrative ranges that editors must replace after verification.
- Title and excerpt must signal it is an educational / concept narrative when helpful (e.g. include "Concept Case Study" context in excerpt wording).
- Body sections should use cautious language ("may", "often", "typically").
- Include originality_notes and substantive fact_check_notes telling editors what to verify before publishing.
- challenge, solution, implementation, lessons_learned may use simple HTML: p, br, strong, em, ul, ol, li, a (href http/https only), h2, h3, blockquote.

technology_stack: array of short strings (languages/frameworks/services).
outcomes: array of short bullet strings (non-quantified claims ok).
metrics: array of objects {"label": string, "note": string} where note explains illustrative intent only.

JSON keys:
title, excerpt, case_study_type (must be "concept"), industry, project_type, challenge, solution, implementation,
technology_stack (array), outcomes (array), metrics (array of {label,note}), lessons_learned,
meta_title, meta_description, meta_keywords, category, subcategory, tags (array),
suggested_slug, source_topic, client_display_name (generic illustrative label, not a real trademark),
image_prompt, video_search_hint, originality_notes, fact_check_notes, reading_time_minutes (integer).
PROMPT;
    }

    private function userPrompt(string $topic): string
    {
        return 'Draft a concept case study outline useful for Artixcore readers. Topic angle: '.$topic;
    }

    private function resolveTopicHint(?string $adminTopic): string
    {
        $t = trim((string) $adminTopic);
        if ($t !== '') {
            return $t;
        }
        $pool = config('ai_content.case_study_topics', []);

        return is_array($pool) && $pool !== [] ? (string) $pool[array_rand($pool)] : 'Enterprise digital initiative';
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
            throw new LlmTransportException('Case study AI response was not valid JSON.');
        }

        return $decoded;
    }

    private function composeStructuredBody(string $challenge, string $solution, string $implementation, string $lessons): string
    {
        $blocks = [];
        if ($challenge !== '') {
            $blocks[] = '<h2>The challenge</h2>'.$challenge;
        }
        if ($solution !== '') {
            $blocks[] = '<h2>The solution</h2>'.$solution;
        }
        if ($implementation !== '') {
            $blocks[] = '<h2>Implementation process</h2>'.$implementation;
        }
        if ($lessons !== '') {
            $blocks[] = '<h2>Lessons learned</h2>'.$lessons;
        }

        return implode("\n", $blocks);
    }

    /**
     * @return list<string>
     */
    private function normalizeStringList(mixed $raw): array
    {
        if (is_string($raw)) {
            $parts = array_map(trim(...), explode(',', $raw));

            return array_values(array_filter($parts, fn ($s) => $s !== ''));
        }
        if (! is_array($raw)) {
            return [];
        }

        $out = [];
        foreach ($raw as $item) {
            $s = trim((string) $item);
            if ($s !== '') {
                $out[] = $s;
            }
        }

        return $out;
    }

    /**
     * @return list<array{label: string, note: string}>
     */
    private function normalizeMetrics(mixed $raw): array
    {
        if (! is_array($raw)) {
            return [];
        }
        $out = [];
        foreach ($raw as $row) {
            if (! is_array($row)) {
                continue;
            }
            $label = trim((string) ($row['label'] ?? ''));
            $note = trim((string) ($row['note'] ?? ''));
            if ($label === '' && $note === '') {
                continue;
            }
            $out[] = ['label' => $label !== '' ? $label : 'Indicator', 'note' => $note];
        }

        return $out;
    }
}
