<?php

namespace App\Providers;

use App\Models\AiRun;
use App\Models\MicroTool;
use App\Models\SiteSetting;
use App\Models\User;
use App\Observers\AiRunObserver;
use App\Observers\MicroToolObserver;
use App\Services\SeoSettingsService;
use App\Services\WebNavigationService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        AiRun::observe(AiRunObserver::class);
        MicroTool::observe(MicroToolObserver::class);

        Gate::before(function (?User $user, string $ability) {
            return $user?->hasRole('master_admin') ? true : null;
        });

        View::composer('*', function ($view): void {
            $view->with('site', SiteSetting::instance());
        });

        View::composer('layouts.app', function ($view): void {
            $nav = app(WebNavigationService::class);
            $primaryNavLinks = $nav->primaryLinks();
            $view->with('primaryNavLinks', $primaryNavLinks);
            $view->with('headerMegaContext', $nav->megaMenuContext($primaryNavLinks));
            $view->with('footerNavLinks', $nav->footerLinks());

            $site = $view->getData()['site'] ?? SiteSetting::instance();
            if ($site instanceof SiteSetting) {
                $site->loadMissing('ogDefaultMedia');
            }
            $seo = app(SeoSettingsService::class);
            $view->with('seoHead', $seo->resolvedHeadMeta($site));
            $view->with('seoScripts', $seo->resolvedScripts());
        });
    }
}
