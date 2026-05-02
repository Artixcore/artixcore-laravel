<?php

use App\Http\Middleware\EnsureBladeAdminAccess;
use App\Http\Middleware\EnsureBuilderAccess;
use App\Http\Middleware\OptionalSanctumAuth;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withSchedule(function (Schedule $schedule): void {
        $schedule->command('micro-tools:aggregate-daily-stats')->dailyAt('01:00');
        $schedule->command('pages:publish-scheduled')->everyMinute();
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
        ]);

        $middleware->redirectGuestsTo(fn () => route('login'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
