<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set(
            'Permissions-Policy',
            'camera=(), microphone=(), geolocation=(), payment=(), usb=()'
        );

        if ($request->secure() && app()->environment('production')) {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains'
            );
        }

        if (! $response->headers->has('Content-Security-Policy')) {
            $response->headers->set('Content-Security-Policy', $this->contentSecurityPolicy($request));
        }

        return $response;
    }

    private function contentSecurityPolicy(Request $request): string
    {
        $directives = [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' https://challenges.cloudflare.com",
            "script-src-elem 'self' 'unsafe-inline' https://challenges.cloudflare.com",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com",
            "img-src 'self' data: https:",
            "font-src 'self' data: https://fonts.gstatic.com",
            'frame-src https://challenges.cloudflare.com',
            "child-src https://challenges.cloudflare.com",
            "worker-src 'self' blob:",
            "connect-src 'self' https://challenges.cloudflare.com",
            "base-uri 'self'",
            "form-action 'self'",
            "frame-ancestors 'self'",
        ];

        if (app()->environment('local')) {
            $directives[2] = "script-src 'self' 'unsafe-inline' https://challenges.cloudflare.com http://127.0.0.1:* http://localhost:*";
            $directives[3] = "script-src-elem 'self' 'unsafe-inline' https://challenges.cloudflare.com http://127.0.0.1:* http://localhost:*";
            $directives[4] = "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com http://127.0.0.1:* http://localhost:*";
            $directives[6] = "font-src 'self' data: https://fonts.gstatic.com http://127.0.0.1:* http://localhost:*";
            $directives[9] = "connect-src 'self' https://challenges.cloudflare.com ws: http://127.0.0.1:* http://localhost:* http://[::1]:*";
        }

        return implode('; ', $directives);
    }
}
