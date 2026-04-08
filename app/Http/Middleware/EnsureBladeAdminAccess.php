<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureBladeAdminAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->can('filament.access')) {
            abort(403);
        }

        return $next($request);
    }
}
