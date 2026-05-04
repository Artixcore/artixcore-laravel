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
        $normalized = $errors ?: ['email' => [$message]];

        return response()->json([
            'ok' => false,
            'message' => $message,
            'errors' => $normalized,
        ], 422);
    }

    protected function authJsonSuccess(string $redirect, string $message = 'Welcome to Artixcore.'): JsonResponse
    {
        return response()->json([
            'ok' => true,
            'message' => $message,
            'redirect' => $redirect,
        ]);
    }

    protected function authJsonGenericFailure(Request $request): JsonResponse
    {
        $msg = 'These credentials do not match our records.';

        return $this->authJsonValidationError($request, $msg, ['email' => [$msg]]);
    }
}
