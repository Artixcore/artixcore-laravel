<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleGenerationLog;
use App\Models\CaseStudy;
use App\Models\ContactMessage;
use App\Models\Service;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'draftArticles' => Article::query()->where('status', Article::STATUS_DRAFT)->count(),
            'draftCaseStudies' => CaseStudy::query()->where('status', '!=', 'published')->count(),
            'draftServices' => Service::query()->where('status', 'draft')->count(),
            'unreadMessages' => ContactMessage::query()->whereNull('read_at')->count(),
            'articlesTotal' => Article::query()->count(),
            'articlesPublished' => Article::query()->where('status', Article::STATUS_PUBLISHED)->count(),
            'articlesPendingReview' => Article::query()->where('status', Article::STATUS_PENDING_REVIEW)->count(),
            'articlesScheduled' => Article::query()->where('status', Article::STATUS_SCHEDULED)->count(),
            'articlesAiToday' => Article::query()
                ->where('author_type', Article::AUTHOR_TYPE_AI)
                ->whereDate('created_at', today())
                ->count(),
            'articleGenerationIssuesToday' => ArticleGenerationLog::query()
                ->whereDate('log_date', today())
                ->whereNotNull('error_message')
                ->count(),
        ]);
    }
}
