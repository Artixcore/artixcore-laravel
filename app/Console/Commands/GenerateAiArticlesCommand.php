<?php

namespace App\Console\Commands;

use App\Services\Ai\ArticleGenerationService;
use App\Services\Ai\ContentAiOrchestrator;
use Illuminate\Console\Command;

class GenerateAiArticlesCommand extends Command
{
    protected $signature = 'articles:generate-ai
        {--dry-run : Log intent without calling the API or saving articles}
        {--count= : Maximum drafts to generate this run (capped by daily limit)}
        {--type= : Force article_type bucket (latest_discovery|today_trends|latest_tech)}
        {--topic= : Optional topic hint}';

    protected $description = 'Generate up to the daily limit of AI article drafts (Ali 1.0).';

    public function handle(ContentAiOrchestrator $orchestrator, ArticleGenerationService $articles): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $forcedType = $this->option('type') ? (string) $this->option('type') : null;
        $forcedTopic = $this->option('topic') ? (string) $this->option('topic') : null;
        $countOpt = $this->option('count');
        $batchLimit = $countOpt !== null && $countOpt !== '' ? max(0, (int) $countOpt) : null;

        if ($dryRun) {
            $remaining = $articles->remainingDailySlots();
            $would = $batchLimit !== null ? min($remaining, $batchLimit) : $remaining;
            $this->info("Dry run: would generate up to {$would} draft(s) (daily remaining: {$remaining}).");

            return self::SUCCESS;
        }

        if (! $articles->providerConfigured()) {
            $this->warn('No OPENAI_API_KEY or enabled AI provider — skipping.');

            return self::SUCCESS;
        }

        $result = $orchestrator->run(false, 'articles', $forcedType, $forcedTopic, false, $batchLimit);

        foreach ($result['errors'] as $err) {
            $this->warn($err);
        }

        return self::SUCCESS;
    }
}
