<?php

namespace App\Services\Admin;

use App\Models\Article;
use App\Models\ArticleGenerationLog;
use App\Models\CaseStudy;
use App\Models\ContactMessage;
use App\Models\Faq;
use App\Models\Lead;
use App\Models\MarketUpdate;
use App\Models\Service;
use App\Models\Testimonial;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class DashboardStatsService
{
    /**
     * @return array<string, int>
     */
    public function summary(): array
    {
        $defaults = [
            'leads_total' => 0,
            'leads_new' => 0,
            'articles_published' => 0,
            'articles_draft' => 0,
            'services' => 0,
            'saas_platforms' => 0,
            'portfolio_items' => 0,
            'case_studies' => 0,
            'testimonials' => 0,
            'faqs' => 0,
            'users' => 0,
        ];

        try {
            if (Schema::hasTable('leads')) {
                $defaults['leads_total'] = (int) Lead::query()->count();
                $defaults['leads_new'] = (int) Lead::query()->where('status', Lead::STATUS_NEW)->count();
            }
        } catch (\Throwable) {
        }

        try {
            if (Schema::hasTable('articles')) {
                $defaults['articles_published'] = (int) Article::query()->where('status', Article::STATUS_PUBLISHED)->count();
                $defaults['articles_draft'] = (int) Article::query()->where('status', Article::STATUS_DRAFT)->count();
            }
        } catch (\Throwable) {
        }

        try {
            if (Schema::hasTable('services')) {
                $defaults['services'] = (int) Service::query()->count();
            }
        } catch (\Throwable) {
        }

        try {
            if (Schema::hasTable('portfolio_items')) {
                $defaults['portfolio_items'] = (int) \App\Models\PortfolioItem::query()->count();
            }
        } catch (\Throwable) {
        }

        try {
            if (Schema::hasTable('case_studies')) {
                $defaults['case_studies'] = (int) CaseStudy::query()->count();
            }
        } catch (\Throwable) {
        }

        try {
            if (Schema::hasTable('testimonials')) {
                $defaults['testimonials'] = (int) Testimonial::query()->count();
            }
        } catch (\Throwable) {
        }

        try {
            if (Schema::hasTable('faqs')) {
                $defaults['faqs'] = (int) Faq::query()->count();
            }
        } catch (\Throwable) {
        }

        try {
            if (Schema::hasTable('users')) {
                $defaults['users'] = (int) User::query()->count();
            }
        } catch (\Throwable) {
        }

        $defaults['saas_platforms'] = $defaults['services'];

        return $defaults;
    }

    /**
     * @return array{
     *   draftArticles: int,
     *   draftCaseStudies: int,
     *   draftMarketUpdates: int,
     *   draftServices: int,
     *   unreadMessages: int,
     *   articlesTotal: int,
     *   articlesPublished: int,
     *   articlesPendingReview: int,
     *   articlesScheduled: int,
     *   articlesAiToday: int,
     *   caseStudiesAiWeek: int,
     *   marketUpdatesAiWeek: int,
     *   caseStudiesPendingReview: int,
     *   marketUpdatesPendingReview: int,
     *   articleGenerationIssuesToday: int,
     *   lastContentGenerationLog: ArticleGenerationLog|null
     * }
     */
    public function detailed(): array
    {
        $weekStart = Carbon::now()->startOfWeek();
        $defaults = [
            'draftArticles' => 0,
            'draftCaseStudies' => 0,
            'draftMarketUpdates' => 0,
            'draftServices' => 0,
            'unreadMessages' => 0,
            'articlesTotal' => 0,
            'articlesPublished' => 0,
            'articlesPendingReview' => 0,
            'articlesScheduled' => 0,
            'articlesAiToday' => 0,
            'caseStudiesAiWeek' => 0,
            'marketUpdatesAiWeek' => 0,
            'caseStudiesPendingReview' => 0,
            'marketUpdatesPendingReview' => 0,
            'articleGenerationIssuesToday' => 0,
            'lastContentGenerationLog' => null,
        ];

        try {
            if (Schema::hasTable('articles')) {
                $defaults['draftArticles'] = (int) Article::query()->where('status', Article::STATUS_DRAFT)->count();
                $defaults['articlesTotal'] = (int) Article::query()->count();
                $defaults['articlesPublished'] = (int) Article::query()->where('status', Article::STATUS_PUBLISHED)->count();
                $defaults['articlesPendingReview'] = (int) Article::query()->where('status', Article::STATUS_PENDING_REVIEW)->count();
                $defaults['articlesScheduled'] = (int) Article::query()->where('status', Article::STATUS_SCHEDULED)->count();
                $defaults['articlesAiToday'] = (int) Article::query()
                    ->where('author_type', Article::AUTHOR_TYPE_AI)
                    ->whereDate('created_at', today())
                    ->count();
            }
        } catch (\Throwable) {
        }

        try {
            if (Schema::hasTable('case_studies')) {
                $defaults['draftCaseStudies'] = (int) CaseStudy::query()->where('status', '!=', CaseStudy::STATUS_PUBLISHED)->count();
                $defaults['caseStudiesAiWeek'] = (int) CaseStudy::query()
                    ->where('author_type', CaseStudy::AUTHOR_TYPE_AI)
                    ->where('created_at', '>=', $weekStart)
                    ->count();
                $defaults['caseStudiesPendingReview'] = (int) CaseStudy::query()->where('status', CaseStudy::STATUS_PENDING_REVIEW)->count();
            }
        } catch (\Throwable) {
        }

        try {
            if (Schema::hasTable('market_updates')) {
                $defaults['draftMarketUpdates'] = (int) MarketUpdate::query()->where('status', '!=', MarketUpdate::STATUS_PUBLISHED)->count();
                $defaults['marketUpdatesAiWeek'] = (int) MarketUpdate::query()
                    ->where('author_type', MarketUpdate::AUTHOR_TYPE_AI)
                    ->where('created_at', '>=', $weekStart)
                    ->count();
                $defaults['marketUpdatesPendingReview'] = (int) MarketUpdate::query()->where('status', MarketUpdate::STATUS_PENDING_REVIEW)->count();
            }
        } catch (\Throwable) {
        }

        try {
            if (Schema::hasTable('services')) {
                $defaults['draftServices'] = (int) Service::query()->where('status', 'draft')->count();
            }
        } catch (\Throwable) {
        }

        try {
            if (Schema::hasTable('contact_messages')) {
                $defaults['unreadMessages'] = (int) ContactMessage::query()->whereNull('read_at')->count();
            }
        } catch (\Throwable) {
        }

        try {
            if (Schema::hasTable('article_generation_logs')) {
                $defaults['articleGenerationIssuesToday'] = (int) ArticleGenerationLog::query()
                    ->whereDate('log_date', today())
                    ->whereNotNull('error_message')
                    ->count();
                $defaults['lastContentGenerationLog'] = ArticleGenerationLog::query()->orderByDesc('id')->first();
            }
        } catch (\Throwable) {
        }

        return $defaults;
    }
}
