<?php

namespace App\Console\Commands;

use App\Services\ProductionDomainRepairService;
use Illuminate\Console\Command;

class RepairProductionContentCommand extends Command
{
    protected $signature = 'artixcore:repair-production-content';

    protected $description = 'Replace legacy artixcore.test URLs and emails in CMS/settings tables (idempotent)';

    public function handle(ProductionDomainRepairService $repair): int
    {
        $summary = $repair->run($this);

        if ($summary['by_table'] !== []) {
            $this->newLine();
            $this->info('Updates per table:');
            foreach ($summary['by_table'] as $table => $count) {
                $this->line("  {$table}: {$count} row(s) touched");
            }
        }

        return self::SUCCESS;
    }
}
