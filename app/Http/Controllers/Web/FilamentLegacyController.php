<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

class FilamentLegacyController extends Controller
{
    public function __invoke(): RedirectResponse|Response
    {
        if (config('artixcore.filament_legacy_redirect', 'admin_login') === 'not_found') {
            abort(404);
        }

        if (! Route::has('admin.login')) {
            abort(404);
        }

        return redirect()->route('admin.login', [], 302);
    }
}
