<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Session portal for customers: portal.access without Blade admin access.
 */
class EnsurePortalUser
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if ($user === null) {
            abort(403);
        }

        if (! $user->can('portal.access')) {
            abort(403);
        }

        if ($user->can('admin.access')) {
            abort(403);
        }

        return $next($request);
    }
}
