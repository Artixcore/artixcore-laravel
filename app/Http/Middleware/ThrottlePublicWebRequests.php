<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Symfony\Component\HttpFoundation\Response;

/**
 * Applies the "public-web" rate limiter to the web stack while skipping admin,
 * Filament, Livewire, builder, and health routes.
 */
class ThrottlePublicWebRequests
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($this->shouldSkip($request)) {
            return $next($request);
        }

        return app(ThrottleRequests::class)->handle($request, $next, 'public-web');
    }

    private function shouldSkip(Request $request): bool
    {
        $path = strtolower(trim($request->path(), '/'));

        $prefixes = [
            'filament',
            'livewire',
            'admin',
            'builder',
            'builder-api',
            'sanctum',
        ];

        foreach ($prefixes as $prefix) {
            if ($path === $prefix || str_starts_with($path, $prefix.'/')) {
                return true;
            }
        }

        if ($path === 'up' || $path === 'health') {
            return true;
        }

        return false;
    }
}
