<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\CaseStudy;
use App\Models\ContactMessage;
use App\Models\Service;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'draftArticles' => Article::query()->where('status', '!=', 'published')->count(),
            'draftCaseStudies' => CaseStudy::query()->where('status', '!=', 'published')->count(),
            'draftServices' => Service::query()->where('status', 'draft')->count(),
            'unreadMessages' => ContactMessage::query()->whereNull('read_at')->count(),
        ]);
    }
}
