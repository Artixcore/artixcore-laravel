<?php

namespace App\Console\Commands;

use App\Models\ArticleGenerationLog;
use App\Services\Ai\ArticleGenerationService;
use App\Services\Ai\Exceptions\LlmTransportException;
use Illuminate\Console\Command;
use Throwable;

class GenerateAiArticlesCommand extends Command
{
    protected $signature = 'articles:generate-ai
        {--dry-run : Log intent without calling the API or saving articles}
        {--type= : Force article_type bucket (latest_discovery|today_trends|latest_tech)}
        {--topic= : Optional topic hint}';

    protected $description = 'Generate up to the daily limit of AI article drafts (Ali 1.0).';

    public function handle(ArticleGenerationService $service): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $forcedType = $this->option('type') ? (string) $this->option('type') : null;
        $forcedTopic = $this->option('topic') ? (string) $this->option('topic') : null;

        if (! $service->providerConfigured()) {
            $this->warn('No OPENAI_API_KEY or enabled AI provider — skipping.');
            $this->writeLog('skipped', 0, 'No API key configured.', $forcedType);

            return self::SUCCESS;
        }

        $remaining = $service->remainingDailySlots();
        if ($remaining <= 0) {
            $this->info('Daily AI article limit reached — skipping.');
            $this->writeLog('skipped', 0, 'Daily limit reached.', $forcedType);

            return self::SUCCESS;
        }

        if ($dryRun) {
            $this->info("Dry run: would generate up to {$remaining} draft(s).");

            return self::SUCCESS;
        }

        $created = 0;
        $lastError = null;

        for ($i = 0; $i < $remaining; $i++) {
            if ($service->remainingDailySlots() <= 0) {
                break;
            }

            try {
                $article = $service->generateScheduledDraft($forcedType, $forcedTopic);
                if ($article === null) {
                    break;
                }
                $created++;
                $this->info("Created draft #{$article->id} — {$article->title}");
            } catch (LlmTransportException $e) {
                $lastError = $e->getMessage();
                $this->warn('Transport error: '.$lastError);
                break;
            } catch (Throwable $e) {
                $lastError = $e->getMessage();
                report($e);
                $this->error('Failed: '.$lastError);
                break;
            }
        }

        $this->writeLog($created > 0 ? 'success' : 'partial', $created, $lastError, $forcedType);

        return self::SUCCESS;
    }

    private function writeLog(string $status, int $created, ?string $error, ?string $type): void
    {
        ArticleGenerationLog::query()->create([
            'log_date' => now()->toDateString(),
            'status' => $status,
            'article_type' => $type,
            'error_message' => $error,
            'articles_created' => $created,
            'metadata' => ['runner' => 'articles:generate-ai'],
        ]);
    }
}
