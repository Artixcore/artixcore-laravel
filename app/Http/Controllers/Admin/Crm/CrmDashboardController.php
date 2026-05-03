<?php

namespace App\Http\Controllers\Admin\Crm;

use App\Http\Controllers\Controller;
use App\Services\Admin\CrmDashboardStatsService;
use Illuminate\View\View;

class CrmDashboardController extends Controller
{
    public function __construct(
        private CrmDashboardStatsService $stats,
    ) {}

    public function index(): View
    {
        $this->authorize('viewAny', \App\Models\CrmContact::class);

        return view('admin.crm.dashboard', $this->stats->stats());
    }
}
