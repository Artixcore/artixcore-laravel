<?php

namespace App\Providers;

use App\Models\AiRun;
use App\Models\User;
use App\Observers\AiRunObserver;
use Illuminate\Support\Facades\Gate;
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

        Gate::before(function (?User $user, string $ability) {
            return $user?->hasRole('master_admin') ? true : null;
        });
    }
}
