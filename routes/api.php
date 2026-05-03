<?php

use App\Http\Controllers\Api\V1\AiChatController;
use App\Http\Controllers\Api\V1\AnalyticsEventController;
use App\Http\Controllers\Api\V1\ArticleController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CaseStudyController;
use App\Http\Controllers\Api\V1\ContactController;
use App\Http\Controllers\Api\V1\MetaController;
use App\Http\Controllers\Api\V1\NavigationController;
use App\Http\Controllers\Api\V1\PageController;
use App\Http\Controllers\Api\V1\Portal\MeController;
use App\Http\Controllers\Api\V1\Portal\ProfileController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\RelatedContentController;
use App\Http\Controllers\Api\V1\ResearchPaperController;
use App\Http\Controllers\Api\V1\SeoSettingController;
use App\Http\Controllers\Api\V1\SiteController;
use App\Http\Controllers\Api\V1\TeamProfileController;
use App\Http\Controllers\Api\V1\Tools\ToolCatalogController;
use App\Http\Controllers\Api\V1\Tools\ToolMeController;
use App\Http\Controllers\Api\V1\Tools\ToolRunController;
use App\Http\Controllers\Api\V1\Tools\ToolSessionController;
use App\Http\Controllers\Api\V1\TrendingController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::get('/ai/agents/{slug}/profile', [AiChatController::class, 'profile'])
        ->middleware('throttle:120,1')
        ->where('slug', '[a-z0-9\-]+')
        ->name('api.v1.ai.agents.profile');
    Route::post('/ai/chat', [AiChatController::class, 'chat'])
        ->middleware('throttle:ai-chat-minute')
        ->name('api.v1.ai.chat');

    Route::post('/auth/login', [AuthController::class, 'login'])
        ->middleware('throttle:10,1')
        ->name('api.v1.auth.login');

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::post('/auth/logout', [AuthController::class, 'logout'])->name('api.v1.auth.logout');
        Route::get('/portal/me', MeController::class)->name('api.v1.portal.me');

        Route::get('/portal/profile', [ProfileController::class, 'show'])->name('api.v1.portal.profile.show');
        Route::patch('/portal/profile', [ProfileController::class, 'updateProfile'])->name('api.v1.portal.profile.update');
        Route::put('/portal/profile/password', [ProfileController::class, 'updatePassword'])->name('api.v1.portal.profile.password');
        Route::post('/portal/profile/avatar', [ProfileController::class, 'uploadAvatar'])
            ->middleware('throttle:30,1')
            ->name('api.v1.portal.profile.avatar.upload');
        Route::delete('/portal/profile/avatar', [ProfileController::class, 'removeAvatar'])->name('api.v1.portal.profile.avatar.remove');
        Route::get('/portal/profile/photos', [ProfileController::class, 'listPhotos'])->name('api.v1.portal.profile.photos.index');
        Route::post('/portal/profile/photos', [ProfileController::class, 'uploadPhoto'])
            ->middleware('throttle:30,1')
            ->name('api.v1.portal.profile.photos.store');
        Route::delete('/portal/profile/photos/{media}', [ProfileController::class, 'deletePhoto'])
            ->whereNumber('media')
            ->name('api.v1.portal.profile.photos.destroy');
    });

    Route::get('/health', fn () => ['status' => 'ok', 'timestamp' => now()->toIso8601String()]);

    Route::get('/site', [SiteController::class, 'show'])->name('api.v1.site.show');

    Route::get('/seo-settings', [SeoSettingController::class, 'show'])->name('api.v1.seo-settings.show');
    Route::put('/seo-settings', [SeoSettingController::class, 'update'])
        ->middleware('auth:sanctum')
        ->name('api.v1.seo-settings.update');

    Route::get('/meta/block-types', [MetaController::class, 'blockTypes'])
        ->name('api.v1.meta.block-types');

    Route::post('/contact', [ContactController::class, 'store'])
        ->middleware('throttle:lead-forms');

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
        ->middleware('throttle:analytics-ingest');

    Route::get('/tools', [ToolCatalogController::class, 'index'])->name('api.v1.tools.index');
    Route::get('/tools/session', ToolSessionController::class)
        ->middleware(['optional.sanctum'])
        ->name('api.v1.tools.session');
    Route::get('/tools/{slug}', [ToolCatalogController::class, 'show'])
        ->where('slug', '[a-z0-9\-]+')
        ->name('api.v1.tools.show');
    Route::post('/tools/{slug}/run', ToolRunController::class)
        ->middleware(['optional.sanctum', 'throttle:60,1'])
        ->where('slug', '[a-z0-9\-]+')
        ->name('api.v1.tools.run');

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::get('/tools/me/favorites', [ToolMeController::class, 'favorites'])->name('api.v1.tools.me.favorites');
        Route::post('/tools/me/favorites/{toolId}', [ToolMeController::class, 'favoriteStore'])
            ->whereNumber('toolId')
            ->name('api.v1.tools.me.favorites.store');
        Route::delete('/tools/me/favorites/{toolId}', [ToolMeController::class, 'favoriteDestroy'])
            ->whereNumber('toolId')
            ->name('api.v1.tools.me.favorites.destroy');
        Route::get('/tools/me/history', [ToolMeController::class, 'history'])->name('api.v1.tools.me.history');
        Route::get('/tools/me/reports', [ToolMeController::class, 'reports'])->name('api.v1.tools.me.reports');
        Route::get('/tools/me/reports/{id}', [ToolMeController::class, 'reportShow'])
            ->whereNumber('id')
            ->name('api.v1.tools.me.reports.show');
        Route::post('/tools/me/reports', [ToolMeController::class, 'reportStore'])->name('api.v1.tools.me.reports.store');
    });
});
