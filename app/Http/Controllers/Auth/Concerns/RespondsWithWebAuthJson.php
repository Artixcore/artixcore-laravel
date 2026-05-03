<?php

namespace App\Http\Controllers\Auth\Concerns;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

trait RespondsWithWebAuthJson
{
    protected function wantsAuthJson(Request $request): bool
    {
        return $request->expectsJson()
            || $request->boolean('ajax')
            || $request->header('X-Requested-With') === 'XMLHttpRequest';
    }

    protected function authJsonValidationError(Request $request, string $message, array $errors = []): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'errors' => $errors ?: ['email' => [$message]],
        ], 422);
    }

    protected function authJsonSuccess(string $redirect): JsonResponse
    {
        return response()->json([
            'ok' => true,
            'redirect' => $redirect,
        ]);
    }

    protected function authJsonGenericFailure(Request $request): JsonResponse
    {
        $msg = 'These credentials do not match our records.';

        return $this->authJsonValidationError($request, $msg, ['email' => [$msg]]);
    }
}
