<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ActivityLog;
use App\Models\AdminAccessRule;
use App\Services\Admin\DashboardStatsService;
use App\Services\Security\AdminAccessControlService;
use Illuminate\View\View;

class MasterDashboardController extends Controller
{
    public function __construct(
        private DashboardStatsService $stats,
        private AdminAccessControlService $accessControl,
    ) {}

    public function index(): View
    {
        $stats = $this->stats->summary();
        $recentLogs = ActivityLog::query()
            ->orderByDesc('id')
            ->limit(15)
            ->get();

        $adminUsers = User::query()
            ->with('roles')
            ->whereHas('roles', fn ($q) => $q->whereIn('name', ['admin', 'master_admin', 'content_admin', 'marketing_admin']))
            ->orderBy('name')
            ->limit(20)
            ->get();

        return view('master.dashboard', [
            'stats' => $stats,
            'recentLogs' => $recentLogs,
            'adminUsers' => $adminUsers,
            'ipRulesActiveAdmin' => $this->accessControl->hasActiveRulesFor(AdminAccessRule::AREA_ADMIN),
            'ipRulesActiveMaster' => $this->accessControl->hasActiveRulesFor(AdminAccessRule::AREA_MASTER),
        ]);
    }
}
