<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleGenerationLog;
use App\Models\CaseStudy;
use App\Models\ContactMessage;
use App\Models\MarketUpdate;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $weekStart = Carbon::now()->startOfWeek();

        return view('admin.dashboard', [
            'draftArticles' => Article::query()->where('status', Article::STATUS_DRAFT)->count(),
            'draftCaseStudies' => CaseStudy::query()->where('status', '!=', CaseStudy::STATUS_PUBLISHED)->count(),
            'draftMarketUpdates' => MarketUpdate::query()->where('status', '!=', MarketUpdate::STATUS_PUBLISHED)->count(),
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
            'caseStudiesAiWeek' => CaseStudy::query()
                ->where('author_type', CaseStudy::AUTHOR_TYPE_AI)
                ->where('created_at', '>=', $weekStart)
                ->count(),
            'marketUpdatesAiWeek' => MarketUpdate::query()
                ->where('author_type', MarketUpdate::AUTHOR_TYPE_AI)
                ->where('created_at', '>=', $weekStart)
                ->count(),
            'caseStudiesPendingReview' => CaseStudy::query()->where('status', CaseStudy::STATUS_PENDING_REVIEW)->count(),
            'marketUpdatesPendingReview' => MarketUpdate::query()->where('status', MarketUpdate::STATUS_PENDING_REVIEW)->count(),
            'articleGenerationIssuesToday' => ArticleGenerationLog::query()
                ->whereDate('log_date', today())
                ->whereNotNull('error_message')
                ->count(),
            'lastContentGenerationLog' => ArticleGenerationLog::query()->orderByDesc('id')->first(),
        ]);
    }
}
