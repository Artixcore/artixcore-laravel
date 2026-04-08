<?php

namespace App\Providers;

use App\Models\AiRun;
use App\Models\MicroTool;
use App\Models\SiteSetting;
use App\Models\User;
use App\Observers\AiRunObserver;
use App\Observers\MicroToolObserver;
use App\Services\WebNavigationService;
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
        AiRun::observe(AiRunObserver::class);
        MicroTool::observe(MicroToolObserver::class);

        Gate::before(function (?User $user, string $ability) {
            return $user?->hasRole('master_admin') ? true : null;
        });

        View::composer(['layouts.app', 'layouts.admin', 'errors.404'], function ($view): void {
            $view->with('site', SiteSetting::instance());
        });

        View::composer('layouts.app', function ($view): void {
            $nav = app(WebNavigationService::class);
            $view->with('primaryNavLinks', $nav->primaryLinks());
            $view->with('footerNavLinks', $nav->footerLinks());
        });
    }
}
