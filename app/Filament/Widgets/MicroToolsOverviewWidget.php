<?php

namespace App\Filament\Widgets;

use App\Models\MicroTool;
use App\Models\MicroToolCategory;
use App\Models\MicroToolRun;
use App\Models\MicroToolUsageDailyStat;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MicroToolsOverviewWidget extends StatsOverviewWidget
{
    protected ?string $heading = 'Micro tools';

    public static function canView(): bool
    {
        return auth()->user()?->can('micro_tool_analytics.view') ?? false;
    }

    /**
     * @return array<Stat>
     */
    protected function getStats(): array
    {
        $totalTools = MicroTool::query()->count();
        $activeTools = MicroTool::query()->where('is_active', true)->count();
        $categories = MicroToolCategory::query()->count();

        $runsLast7 = MicroToolRun::query()
            ->where('created_at', '>=', now()->subDays(7))
            ->count();

        $guestRuns7 = MicroToolRun::query()
            ->where('created_at', '>=', now()->subDays(7))
            ->where('is_guest', true)
            ->count();

        $paidRuns7 = MicroToolRun::query()
            ->where('created_at', '>=', now()->subDays(7))
            ->where('is_paid_user', true)
            ->count();

        $ads7 = (int) (MicroToolRun::query()
            ->where('created_at', '>=', now()->subDays(7))
            ->where('ads_shown', true)
            ->count());

        $failed7 = MicroToolRun::query()
            ->where('created_at', '>=', now()->subDays(7))
            ->where('status', 'failed')
            ->count();

        $aggregatedRuns = MicroToolUsageDailyStat::query()
            ->where('stat_date', '>=', now()->subDays(30)->toDateString())
            ->sum('total_runs');

        return [
            Stat::make('Total tools', $totalTools),
            Stat::make('Active tools', $activeTools),
            Stat::make('Disabled tools', max(0, $totalTools - $activeTools)),
            Stat::make('Categories', $categories),
            Stat::make('Runs (7d)', $runsLast7),
            Stat::make('Guest runs (7d)', $guestRuns7),
            Stat::make('Paid runs (7d)', $paidRuns7),
            Stat::make('Ad impressions (7d)', $ads7),
            Stat::make('Failed runs (7d)', $failed7),
            Stat::make('Runs in daily stats (30d roll-up)', $aggregatedRuns ?: '—'),
        ];
    }
}
