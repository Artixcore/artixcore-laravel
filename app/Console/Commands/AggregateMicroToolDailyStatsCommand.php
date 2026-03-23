<?php

namespace App\Console\Commands;

use App\Models\MicroSavedReport;
use App\Models\MicroTool;
use App\Models\MicroToolUsageDailyStat;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AggregateMicroToolDailyStatsCommand extends Command
{
    protected $signature = 'micro-tools:aggregate-daily-stats {--date= : YYYY-MM-DD (default: yesterday UTC)}';

    protected $description = 'Aggregate micro_tool_runs into micro_tool_usage_daily_stats for a calendar day';

    public function handle(): int
    {
        $dateStr = $this->option('date') ?: Carbon::yesterday('UTC')->toDateString();
        $date = Carbon::parse($dateStr, 'UTC')->startOfDay();

        $toolIds = MicroTool::query()->pluck('id');

        foreach ($toolIds as $toolId) {
            $runs = DB::table('micro_tool_runs')
                ->where('micro_tool_id', $toolId)
                ->whereDate('created_at', $date->toDateString());

            $total = (clone $runs)->count();
            if ($total === 0) {
                continue;
            }

            $guestRuns = (clone $runs)->where('is_guest', true)->count();
            $paidRuns = (clone $runs)->where('is_paid_user', true)->count();
            $registeredRuns = (clone $runs)->where('is_registered', true)->count();
            $freeUserRuns = max(0, $registeredRuns - $paidRuns);
            $successRuns = (clone $runs)->where('status', 'success')->count();
            $failedRuns = (clone $runs)->where('status', 'failed')->count();
            $adsViews = (clone $runs)->where('ads_shown', true)->count();

            $uniqueUsers = (int) (DB::table('micro_tool_runs')
                ->where('micro_tool_id', $toolId)
                ->whereDate('created_at', $date->toDateString())
                ->whereNotNull('user_id')
                ->selectRaw('count(distinct user_id) as aggregate')
                ->value('aggregate') ?? 0);

            $uniqueGuests = (int) (DB::table('micro_tool_runs')
                ->where('micro_tool_id', $toolId)
                ->whereDate('created_at', $date->toDateString())
                ->whereNotNull('guest_token')
                ->selectRaw('count(distinct guest_token) as aggregate')
                ->value('aggregate') ?? 0);

            $savedReports = MicroSavedReport::query()
                ->where('micro_tool_id', $toolId)
                ->whereDate('created_at', $date->toDateString())
                ->count();

            MicroToolUsageDailyStat::query()->updateOrCreate(
                [
                    'micro_tool_id' => $toolId,
                    'stat_date' => $date->toDateString(),
                ],
                [
                    'total_runs' => $total,
                    'guest_runs' => $guestRuns,
                    'free_user_runs' => $freeUserRuns,
                    'paid_user_runs' => $paidRuns,
                    'success_runs' => $successRuns,
                    'failed_runs' => $failedRuns,
                    'saved_reports_count' => $savedReports,
                    'ads_views_count' => $adsViews,
                    'unique_users_count' => $uniqueUsers,
                    'unique_guests_count' => $uniqueGuests,
                ]
            );
        }

        $this->info("Aggregated micro tool stats for {$date->toDateString()}.");

        return self::SUCCESS;
    }
}
