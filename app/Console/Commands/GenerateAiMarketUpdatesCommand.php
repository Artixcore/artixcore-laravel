<?php

namespace App\Console\Commands;

use App\Services\Ai\ContentAiOrchestrator;
use App\Services\Ai\Exceptions\LlmTransportException;
use App\Services\Ai\MarketUpdateGenerationService;
use Illuminate\Console\Command;
use Throwable;

class GenerateAiMarketUpdatesCommand extends Command
{
    protected $signature = 'market-updates:generate-ai
        {--dry-run : Log intent without calling the API}
        {--topic= : Optional topic / market area hint}
        {--force : Ignore interval spacing (still respects daily limit)}';

    protected $description = 'Generate up to the daily limit of AI market update drafts (Ali 1.0).';

    public function handle(ContentAiOrchestrator $orchestrator, MarketUpdateGenerationService $service): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $topic = $this->option('topic') ? (string) $this->option('topic') : null;
        $force = (bool) $this->option('force');

        if ($dryRun) {
            $this->info('Dry run: interval gate '.($service->shouldRunScheduledGeneration($force) ? 'open' : 'closed')
                .'; remaining daily '.$service->remainingDailySlots());

            return self::SUCCESS;
        }

        if (! $service->providerConfigured()) {
            $this->warn('No OPENAI_API_KEY or enabled AI provider — skipping.');

            return self::SUCCESS;
        }

        try {
            $result = $orchestrator->run(false, 'market-updates', null, $topic, $force);
            if (($result['market_updates_created'] ?? 0) > 0) {
                $this->info('Market update draft created.');
            } else {
                $this->info('No market update generated (interval, limit, or disabled).');
            }
            foreach ($result['errors'] as $err) {
                $this->warn($err);
            }
        } catch (LlmTransportException $e) {
            $this->warn('Transport error: '.$e->getMessage());
        } catch (Throwable $e) {
            report($e);
            $this->error('Failed: '.$e->getMessage());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
