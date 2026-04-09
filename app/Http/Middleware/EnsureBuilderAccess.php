<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureBuilderAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->can('builder.access')) {
            abort(403, 'Page builder access denied.');
        }

        return $next($request);
    }
}
