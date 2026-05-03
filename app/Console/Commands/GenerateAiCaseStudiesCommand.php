<?php

namespace App\Console\Commands;

use App\Services\Ai\CaseStudyGenerationService;
use App\Services\Ai\ContentAiOrchestrator;
use App\Services\Ai\Exceptions\LlmTransportException;
use Illuminate\Console\Command;
use Throwable;

class GenerateAiCaseStudiesCommand extends Command
{
    protected $signature = 'case-studies:generate-ai
        {--dry-run : Log intent without calling the API}
        {--topic= : Optional topic hint}
        {--force : Ignore interval spacing (still respects daily limit)}';

    protected $description = 'Generate up to the daily limit of AI case study drafts (Ali 1.0).';

    public function handle(ContentAiOrchestrator $orchestrator, CaseStudyGenerationService $service): int
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
            $result = $orchestrator->run(false, 'case-studies', null, $topic, $force);
            if (($result['case_studies_created'] ?? 0) > 0) {
                $this->info('Case study draft queued successfully.');
            } else {
                $this->info('No case study generated (interval, limit, or disabled).');
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
