<?php

use App\Http\Middleware\EnsureAdminIpAllowed;
use App\Http\Middleware\EnsureBladeAdminAccess;
use App\Http\Middleware\EnsureBuilderAccess;
use App\Http\Middleware\EnsureMasterAdmin;
use App\Http\Middleware\EnsureMasterIpAllowed;
use App\Http\Middleware\EnsurePortalUser;
use App\Http\Middleware\RedirectAuthenticatedFromLoginPages;
use App\Http\Middleware\OptionalSanctumAuth;
use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\ThrottleApiGuestOrUser;
use App\Http\Middleware\ThrottlePublicWebRequests;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withSchedule(function (Schedule $schedule): void {
        $schedule->command('micro-tools:aggregate-daily-stats')->dailyAt('01:00');
        $schedule->command('pages:publish-scheduled')->everyMinute();
        $schedule->command('content:generate-ai')->dailyAt('04:00');
    })
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $proxies = env('TRUSTED_PROXIES', '*');
        $at = is_string($proxies) && $proxies !== '' && $proxies !== '*'
            ? array_map(trim(...), explode(',', $proxies))
            : '*';
        $middleware->trustProxies(at: $at);

        $middleware->alias([
            'optional.sanctum' => OptionalSanctumAuth::class,
            'blade.admin' => EnsureBladeAdminAccess::class,
            'builder.access' => EnsureBuilderAccess::class,
            'admin.ip' => EnsureAdminIpAllowed::class,
            'master.ip' => EnsureMasterIpAllowed::class,
            'master.panel' => EnsureMasterAdmin::class,
            'portal.user' => EnsurePortalUser::class,
            'login.guest' => RedirectAuthenticatedFromLoginPages::class,
        ]);

        $middleware->redirectGuestsTo(function (Request $request): string {
            if ($request->is('admin') || $request->is('admin/*')) {
                return route('admin.login');
            }

            if ($request->is('master') || $request->is('master/*')) {
                return route('master.login');
            }

            return route('login');
        });

        $middleware->redirectUsersTo(function (): string {
            $user = Auth::user();

            if ($user === null) {
                return Route::has('home') ? route('home') : '/';
            }

            if ($user->hasRole('master_admin')) {
                return route('master.dashboard');
            }

            if ($user->can('admin.access')) {
                return route('admin.dashboard');
            }

            if ($user->can('portal.access')) {
                return route('portal');
            }

            return Route::has('home') ? route('home') : '/';
        });

        $middleware->appendToGroup('web', [
            ThrottlePublicWebRequests::class,
            SecurityHeaders::class,
        ]);

        $middleware->appendToGroup('api', [
            ThrottleApiGuestOrUser::class,
            SecurityHeaders::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
