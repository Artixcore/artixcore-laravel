<?php

namespace App\Console\Commands;

use App\Services\Ai\ArticleGenerationService;
use App\Services\Ai\CaseStudyGenerationService;
use App\Services\Ai\ContentAiOrchestrator;
use App\Services\Ai\MarketUpdateGenerationService;
use Illuminate\Console\Command;

class GenerateAiContentCommand extends Command
{
    protected $signature = 'content:generate-ai
        {--dry-run : Describe work without calling APIs or saving records}
        {--only= : Limit to articles|case-studies|market-updates}
        {--type= : Article bucket (latest_discovery|today_trends|latest_tech) when generating articles}
        {--topic= : Optional topic hint}
        {--force-intervals : Ignore case study / market update spacing rules}';

    protected $description = 'Run Ali 1.0 schedulers: articles (daily cap), case studies & market updates (interval).';

    public function handle(ContentAiOrchestrator $orchestrator): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $only = $this->option('only') ? (string) $this->option('only') : null;
        $topic = $this->option('topic') ? (string) $this->option('topic') : null;
        $forcedType = $this->option('type') ? (string) $this->option('type') : null;
        $forceIntervals = (bool) $this->option('force-intervals');

        if ($only !== null && ! in_array($only, ['articles', 'case-studies', 'market-updates'], true)) {
            $this->error('Invalid --only value. Use articles, case-studies, or market-updates.');

            return self::FAILURE;
        }

        if ($dryRun) {
            $articles = app(ArticleGenerationService::class);
            $caseStudies = app(CaseStudyGenerationService::class);
            $market = app(MarketUpdateGenerationService::class);

            if ($only === null || $only === 'articles') {
                $this->info('Articles: remaining daily slots '.$articles->remainingDailySlots().' (provider '.($articles->providerConfigured() ? 'ok' : 'missing').').');
            }
            if ($only === null || $only === 'case-studies') {
                $this->info('Case studies: enabled='.(config('ai_content.case_study.enabled') ? 'yes' : 'no')
                    .'; would run interval='.($caseStudies->shouldRunScheduledGeneration($forceIntervals) ? 'yes' : 'no')
                    .'; daily remaining='.$caseStudies->remainingDailySlots());
            }
            if ($only === null || $only === 'market-updates') {
                $this->info('Market updates: enabled='.(config('ai_content.market_update.enabled') ? 'yes' : 'no')
                    .'; would run interval='.($market->shouldRunScheduledGeneration($forceIntervals) ? 'yes' : 'no')
                    .'; daily remaining='.$market->remainingDailySlots());
            }

            return self::SUCCESS;
        }

        $result = $orchestrator->run(false, $only, $forcedType, $topic, $forceIntervals);

        $this->info(sprintf(
            'Done — articles: %d, case studies: %d, market updates: %d.',
            $result['articles_created'],
            $result['case_studies_created'],
            $result['market_updates_created']
        ));

        foreach ($result['errors'] as $err) {
            $this->warn($err);
        }

        return self::SUCCESS;
    }
}
