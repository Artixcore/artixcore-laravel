<?php

use App\Http\Controllers\Api\V1\AnalyticsEventController;
use App\Http\Controllers\Api\V1\ArticleController;
use App\Http\Controllers\Api\V1\CaseStudyController;
use App\Http\Controllers\Api\V1\ContactController;
use App\Http\Controllers\Api\V1\NavigationController;
use App\Http\Controllers\Api\V1\PageController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\RelatedContentController;
use App\Http\Controllers\Api\V1\ResearchPaperController;
use App\Http\Controllers\Api\V1\TeamProfileController;
use App\Http\Controllers\Api\V1\TrendingController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::get('/health', fn () => ['status' => 'ok', 'timestamp' => now()->toIso8601String()]);

    Route::post('/contact', [ContactController::class, 'store'])
        ->middleware('throttle:10,1');

    Route::get('/navigation', NavigationController::class);

    Route::get('/pages/{path}', [PageController::class, 'show'])
        ->where('path', '[A-Za-z0-9\-\/]+')
        ->name('api.v1.pages.show');

    Route::get('/articles', [ArticleController::class, 'index'])->name('api.v1.articles.index');
    Route::get('/articles/{slug}', [ArticleController::class, 'show'])->name('api.v1.articles.show');

    Route::get('/research-papers', [ResearchPaperController::class, 'index'])->name('api.v1.research-papers.index');
    Route::get('/research-papers/{slug}', [ResearchPaperController::class, 'show'])->name('api.v1.research-papers.show');

    Route::get('/case-studies', [CaseStudyController::class, 'index'])->name('api.v1.case-studies.index');
    Route::get('/case-studies/{slug}', [CaseStudyController::class, 'show'])->name('api.v1.case-studies.show');

    Route::get('/products', [ProductController::class, 'index'])->name('api.v1.products.index');
    Route::get('/products/{slug}', [ProductController::class, 'show'])->name('api.v1.products.show');

    Route::get('/team', [TeamProfileController::class, 'index'])->name('api.v1.team.index');
    Route::get('/team/{slug}', [TeamProfileController::class, 'show'])->name('api.v1.team.show');

    Route::get('/related', RelatedContentController::class);
    Route::get('/trending', TrendingController::class);

    Route::post('/analytics/events', [AnalyticsEventController::class, 'store'])
        ->middleware('throttle:60,1');
});
