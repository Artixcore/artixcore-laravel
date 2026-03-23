<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class OptionalSanctumAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::guard('sanctum')->check()) {
            $token = $request->bearerToken();
            if (is_string($token) && $token !== '') {
                $accessToken = PersonalAccessToken::findToken($token);
                if ($accessToken !== null && $accessToken->tokenable !== null) {
                    Auth::guard('sanctum')->setUser($accessToken->tokenable);
                }
            }
        }

        return $next($request);
    }
}
