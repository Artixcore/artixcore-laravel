<?php

namespace App\Http\Support;

use Illuminate\Http\Request;

final class AjaxRequestExpectations
{
    public static function prefersJsonResponse(Request $request): bool
    {
        return $request->expectsJson()
            || $request->boolean('ajax')
            || $request->header('X-Requested-With') === 'XMLHttpRequest';
    }
}
