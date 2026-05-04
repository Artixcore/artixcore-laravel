<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Route;

class PostLoginRedirectService
{
    public function url(User $user): string
    {
        if ($user->hasRole('master_admin')) {
            return route('master.dashboard');
        }

        if ($user->can('admin.access')) {
            return route('admin.dashboard');
        }

        if ($user->can('portal.access')) {
            return route('portal');
        }

        return Route::has('home') ? route('home') : '/';
    }
}
