<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\DashboardStatsService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private DashboardStatsService $stats) {}

    public function index(): View
    {
        return view('admin.dashboard', array_merge(
            $this->stats->detailed(),
            ['overview' => $this->stats->summary()]
        ));
    }
}
