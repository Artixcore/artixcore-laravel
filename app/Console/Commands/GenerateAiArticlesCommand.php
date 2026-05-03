<?php

namespace App\Console\Commands;

use App\Services\Ai\ArticleGenerationService;
use App\Services\Ai\ContentAiOrchestrator;
use Illuminate\Console\Command;

class GenerateAiArticlesCommand extends Command
{
    protected $signature = 'articles:generate-ai
        {--dry-run : Log intent without calling the API or saving articles}
        {--type= : Force article_type bucket (latest_discovery|today_trends|latest_tech)}
        {--topic= : Optional topic hint}';

    protected $description = 'Generate up to the daily limit of AI article drafts (Ali 1.0).';

    public function handle(ContentAiOrchestrator $orchestrator, ArticleGenerationService $articles): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $forcedType = $this->option('type') ? (string) $this->option('type') : null;
        $forcedTopic = $this->option('topic') ? (string) $this->option('topic') : null;

        if ($dryRun) {
            $remaining = $articles->remainingDailySlots();
            $this->info("Dry run: would generate up to {$remaining} draft(s).");

            return self::SUCCESS;
        }

        $result = $orchestrator->run(false, 'articles', $forcedType, $forcedTopic, false);

        foreach ($result['errors'] as $err) {
            $this->warn($err);
        }

        return self::SUCCESS;
    }
}
