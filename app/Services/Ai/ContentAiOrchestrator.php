<?php

namespace App\Services\Ai;

use App\Models\ArticleGenerationLog;
use Throwable;

class ContentAiOrchestrator
{
    public function __construct(
        private ArticleGenerationService $articles,
        private CaseStudyGenerationService $caseStudies,
        private MarketUpdateGenerationService $marketUpdates,
    ) {}

    /**
     * @return array{articles_created: int, case_studies_created: int, market_updates_created: int, errors: list<string>}
     */
    public function run(
        bool $dryRun,
        ?string $only,
        ?string $articleType,
        ?string $topic,
        bool $forceIntervals,
    ): array {
        $out = [
            'articles_created' => 0,
            'case_studies_created' => 0,
            'market_updates_created' => 0,
            'errors' => [],
        ];

        $runArticles = $only === null || $only === 'articles';
        $runCaseStudies = $only === null || $only === 'case-studies';
        $runMarket = $only === null || $only === 'market-updates';

        if ($runArticles) {
            $this->runArticles($dryRun, $articleType, $topic, $out);
        }

        if ($runCaseStudies) {
            $this->runCaseStudies($dryRun, $topic, $forceIntervals, $out);
        }

        if ($runMarket) {
            $this->runMarketUpdates($dryRun, $topic, $forceIntervals, $out);
        }

        return $out;
    }

    /**
     * @param  array{articles_created: int, case_studies_created: int, market_updates_created: int, errors: list<string>}  $out
     */
    private function runArticles(bool $dryRun, ?string $articleType, ?string $topic, array &$out): void
    {
        if (! $this->articles->providerConfigured()) {
            ArticleGenerationLog::query()->create([
                'log_date' => now()->toDateString(),
                'status' => 'skipped',
                'content_type' => 'articles',
                'article_type' => $articleType,
                'error_message' => 'No API key configured.',
                'articles_created' => 0,
                'records_created' => 0,
                'metadata' => ['runner' => 'content:generate-ai'],
            ]);

            return;
        }

        $remaining = $this->articles->remainingDailySlots();
        if ($remaining <= 0) {
            ArticleGenerationLog::query()->create([
                'log_date' => now()->toDateString(),
                'status' => 'skipped',
                'content_type' => 'articles',
                'article_type' => $articleType,
                'error_message' => 'Daily limit reached.',
                'articles_created' => 0,
                'records_created' => 0,
                'metadata' => ['runner' => 'content:generate-ai'],
            ]);

            return;
        }

        if ($dryRun) {
            return;
        }

        $lastError = null;
        for ($i = 0; $i < $remaining; $i++) {
            if ($this->articles->remainingDailySlots() <= 0) {
                break;
            }
            try {
                $article = $this->articles->generateScheduledDraft($articleType, $topic);
                if ($article === null) {
                    break;
                }
                $out['articles_created']++;
            } catch (Throwable $e) {
                $lastError = $e->getMessage();
                $out['errors'][] = 'articles: '.$lastError;
                report($e);
                break;
            }
        }

        ArticleGenerationLog::query()->create([
            'log_date' => now()->toDateString(),
            'status' => $out['articles_created'] > 0 ? 'success' : 'partial',
            'content_type' => 'articles',
            'article_type' => $articleType,
            'error_message' => $lastError,
            'articles_created' => $out['articles_created'],
            'records_created' => $out['articles_created'],
            'metadata' => ['runner' => 'content:generate-ai'],
        ]);
    }

    /**
     * @param  array{articles_created: int, case_studies_created: int, market_updates_created: int, errors: list<string>}  $out
     */
    private function runCaseStudies(bool $dryRun, ?string $topic, bool $forceIntervals, array &$out): void
    {
        if (! config('ai_content.case_study.enabled', true)) {
            return;
        }

        if (! $this->caseStudies->providerConfigured()) {
            ArticleGenerationLog::query()->create([
                'log_date' => now()->toDateString(),
                'status' => 'skipped',
                'content_type' => 'case_studies',
                'error_message' => 'No API key configured.',
                'articles_created' => 0,
                'records_created' => 0,
                'metadata' => ['runner' => 'content:generate-ai'],
            ]);

            return;
        }

        if (! $this->caseStudies->shouldRunScheduledGeneration($forceIntervals)) {
            ArticleGenerationLog::query()->create([
                'log_date' => now()->toDateString(),
                'status' => 'skipped',
                'content_type' => 'case_studies',
                'error_message' => 'Interval not elapsed (use --force).',
                'articles_created' => 0,
                'records_created' => 0,
                'metadata' => ['runner' => 'content:generate-ai'],
            ]);

            return;
        }

        if ($this->caseStudies->remainingDailySlots() <= 0) {
            ArticleGenerationLog::query()->create([
                'log_date' => now()->toDateString(),
                'status' => 'skipped',
                'content_type' => 'case_studies',
                'error_message' => 'Daily limit reached.',
                'articles_created' => 0,
                'records_created' => 0,
                'metadata' => ['runner' => 'content:generate-ai'],
            ]);

            return;
        }

        if ($dryRun) {
            return;
        }

        $lastError = null;
        try {
            $study = $this->caseStudies->generateScheduledDraft($topic);
            if ($study !== null) {
                $out['case_studies_created'] = 1;
            }
        } catch (Throwable $e) {
            $lastError = $e->getMessage();
            $out['errors'][] = 'case-studies: '.$lastError;
            report($e);
        }

        ArticleGenerationLog::query()->create([
            'log_date' => now()->toDateString(),
            'status' => $out['case_studies_created'] > 0 ? 'success' : 'partial',
            'content_type' => 'case_studies',
            'error_message' => $lastError,
            'articles_created' => 0,
            'records_created' => $out['case_studies_created'],
            'metadata' => ['runner' => 'content:generate-ai'],
        ]);
    }

    /**
     * @param  array{articles_created: int, case_studies_created: int, market_updates_created: int, errors: list<string>}  $out
     */
    private function runMarketUpdates(bool $dryRun, ?string $topic, bool $forceIntervals, array &$out): void
    {
        if (! config('ai_content.market_update.enabled', true)) {
            return;
        }

        if (! $this->marketUpdates->providerConfigured()) {
            ArticleGenerationLog::query()->create([
                'log_date' => now()->toDateString(),
                'status' => 'skipped',
                'content_type' => 'market_updates',
                'error_message' => 'No API key configured.',
                'articles_created' => 0,
                'records_created' => 0,
                'metadata' => ['runner' => 'content:generate-ai'],
            ]);

            return;
        }

        if (! $this->marketUpdates->shouldRunScheduledGeneration($forceIntervals)) {
            ArticleGenerationLog::query()->create([
                'log_date' => now()->toDateString(),
                'status' => 'skipped',
                'content_type' => 'market_updates',
                'error_message' => 'Interval not elapsed (use --force).',
                'articles_created' => 0,
                'records_created' => 0,
                'metadata' => ['runner' => 'content:generate-ai'],
            ]);

            return;
        }

        if ($this->marketUpdates->remainingDailySlots() <= 0) {
            ArticleGenerationLog::query()->create([
                'log_date' => now()->toDateString(),
                'status' => 'skipped',
                'content_type' => 'market_updates',
                'error_message' => 'Daily limit reached.',
                'articles_created' => 0,
                'records_created' => 0,
                'metadata' => ['runner' => 'content:generate-ai'],
            ]);

            return;
        }

        if ($dryRun) {
            return;
        }

        $lastError = null;
        try {
            $row = $this->marketUpdates->generateScheduledDraft($topic);
            if ($row !== null) {
                $out['market_updates_created'] = 1;
            }
        } catch (Throwable $e) {
            $lastError = $e->getMessage();
            $out['errors'][] = 'market-updates: '.$lastError;
            report($e);
        }

        ArticleGenerationLog::query()->create([
            'log_date' => now()->toDateString(),
            'status' => $out['market_updates_created'] > 0 ? 'success' : 'partial',
            'content_type' => 'market_updates',
            'error_message' => $lastError,
            'articles_created' => 0,
            'records_created' => $out['market_updates_created'],
            'metadata' => ['runner' => 'content:generate-ai'],
        ]);
    }
}
