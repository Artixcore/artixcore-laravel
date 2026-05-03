<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

/**
 * Baseline API throttling: per-IP for guests, per-user id for Sanctum-authenticated requests.
 */
class ThrottleApiGuestOrUser
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user('sanctum');

        $max = $user !== null
            ? max(1, (int) config('rate_limiting.api_authenticated_per_minute', 300))
            : max(1, (int) config('rate_limiting.api_guest_per_minute', 120));

        $key = $user !== null
            ? 'api-auth:'.sha1((string) $user->getAuthIdentifier())
            : 'api-guest:'.sha1((string) $request->ip());

        if (RateLimiter::tooManyAttempts($key, $max)) {
            return response()->json([
                'message' => 'Too many requests. Please slow down.',
            ], 429);
        }

        RateLimiter::hit($key, 60);

        return $next($request);
    }
}
