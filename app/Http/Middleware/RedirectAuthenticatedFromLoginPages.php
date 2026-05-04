<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * When visiting a login page while already authenticated, send the user to the correct home.
 * Replaces the default `guest` middleware for split login routes.
 */
class RedirectAuthenticatedFromLoginPages
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        if ($user === null) {
            return $next($request);
        }

        if ($request->is('master/login')) {
            if ($user->hasRole('master_admin')) {
                return redirect()->route('master.dashboard');
            }

            if ($user->can('admin.access')) {
                return redirect()->route('admin.dashboard');
            }

            if ($user->can('portal.access')) {
                return redirect()->route('portal');
            }

            return redirect()->route('home');
        }

        if ($request->is('admin/login')) {
            if ($user->hasRole('master_admin')) {
                return redirect()->route('master.dashboard');
            }

            if ($user->can('admin.access')) {
                return redirect()->route('admin.dashboard');
            }

            if ($user->can('portal.access')) {
                return redirect()->route('portal');
            }

            return redirect()->route('home');
        }

        if ($request->is('login') || $request->is('register')) {
            if ($user->hasRole('master_admin')) {
                return redirect()->route('master.dashboard');
            }

            if ($user->can('admin.access')) {
                return redirect()->route('admin.dashboard');
            }

            if ($user->can('portal.access')) {
                return redirect()->route('portal');
            }

            return redirect()->route('home');
        }

        return $next($request);
    }
}
