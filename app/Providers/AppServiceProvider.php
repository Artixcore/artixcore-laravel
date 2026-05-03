<?php

namespace App\Providers;

use App\Models\AiRun;
use App\Models\MicroTool;
use App\Models\PlatformSecuritySetting;
use App\Models\SiteSetting;
use App\Models\User;
use App\Observers\AiRunObserver;
use App\Observers\MicroToolObserver;
use App\Services\SeoSettingsService;
use App\Services\WebNavigationService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
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

        RateLimiter::for('public-web', function (Request $request) {
            $perMinute = max(1, (int) config('rate_limiting.public_web_per_minute', 120));

            return Limit::perMinute($perMinute)->by(sha1((string) $request->ip()));
        });

        $leadFormThrottle = function (Request $request) {
            $perMinute = max(1, (int) config('rate_limiting.forms_per_minute', 5));
            $email = strtolower((string) $request->input('email', ''));

            return [
                Limit::perMinute($perMinute)->by(sha1((string) $request->ip())),
                Limit::perMinute($perMinute)->by(sha1((string) $request->ip().'|'.$email)),
            ];
        };

        RateLimiter::for('lead-forms', $leadFormThrottle);
        RateLimiter::for('lead-submit', $leadFormThrottle);

        RateLimiter::for('login-web', function (Request $request) {
            $perMinute = max(1, (int) config('rate_limiting.login_per_minute', 5));
            $email = strtolower((string) $request->input('email', ''));

            return Limit::perMinute($perMinute)->by(sha1((string) $request->ip().'|'.$email));
        });

        RateLimiter::for('analytics-ingest', function (Request $request) {
            $perMinute = max(1, (int) config('rate_limiting.analytics_per_minute', 60));

            return Limit::perMinute($perMinute)->by(sha1((string) $request->ip()));
        });

        RateLimiter::for('ai-chat-minute', function (Request $request) {
            $perMinute = (int) config('rate_limiting.ai_chat_per_minute', 10);
            try {
                $perMinute = (int) PlatformSecuritySetting::instance()->chat_rate_limit_per_minute;
            } catch (\Throwable) {
                //
            }
            $perMinute = max(1, min(1000, $perMinute));
            $token = (string) $request->input('visitor_token', '');

            return Limit::perMinute($perMinute)->by(sha1($request->ip().'|'.$token));
        });

        RateLimiter::for('intake-minute', function (Request $request) {
            $perMinute = max(1, min(120, (int) config('intake.per_minute', 8)));

            return Limit::perMinute($perMinute)->by(sha1($request->ip()));
        });

        RateLimiter::for('builder-ai-minute', function (Request $request) {
            $perMinute = 30;
            try {
                $perMinute = (int) PlatformSecuritySetting::instance()->builder_ai_rate_limit_per_minute;
            } catch (\Throwable) {
                //
            }
            $perMinute = max(1, min(500, $perMinute));

            return Limit::perMinute($perMinute)->by((string) $request->user()?->getAuthIdentifier() ?: $request->ip());
        });

        AiRun::observe(AiRunObserver::class);
        MicroTool::observe(MicroToolObserver::class);

        Gate::before(function (?User $user, string $ability) {
            return $user?->hasRole('master_admin') ? true : null;
        });

        View::composer('*', function ($view): void {
            $view->with('site', SiteSetting::safeInstance());
        });

        View::composer('layouts.app', function ($view): void {
            $primaryNavLinks = [];
            $footerNavLinks = [];
            $headerMegaContext = [
                'services' => collect(),
                'articles' => collect(),
                'caseStudies' => collect(),
            ];
            try {
                $nav = app(WebNavigationService::class);
                $primaryNavLinks = $nav->primaryLinks();
                $headerMegaContext = $nav->megaMenuContext($primaryNavLinks);
                $footerNavLinks = $nav->footerLinks();
            } catch (\Throwable) {
                //
            }
            $view->with('primaryNavLinks', $primaryNavLinks);
            $view->with('headerMegaContext', $headerMegaContext);
            $view->with('footerNavLinks', $footerNavLinks);

            /** @var SiteSetting $site */
            $site = $view->getData()['site'] ?? SiteSetting::safeInstance();
            try {
                $site->loadMissing('ogDefaultMedia');
            } catch (\Throwable) {
                //
            }
            $seo = app(SeoSettingsService::class);
            $view->with('seoHead', $seo->resolvedHeadMeta($site));
            $view->with('seoScripts', $seo->resolvedScripts());
        });
    }
}
