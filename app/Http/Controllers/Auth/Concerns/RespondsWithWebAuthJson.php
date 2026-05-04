<?php

namespace App\Http\Controllers\Auth\Concerns;

use App\Http\Controllers\Concerns\RespondsWithJson;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

trait RespondsWithWebAuthJson
{
    use RespondsWithJson;

    protected function wantsAuthJson(Request $request): bool
    {
        return $this->wantsJsonResponse($request);
    }

    protected function authJsonValidationError(Request $request, string $message, array $errors = []): JsonResponse
    {
        $normalized = $errors ?: ['email' => [$message]];

        return $this->validationErrorResponse($normalized, $message);
    }

    protected function authJsonSuccess(string $redirect, string $message = 'Welcome to Artixcore.'): JsonResponse
    {
        return $this->successResponse($message, [], $redirect);
    }

    protected function authJsonGenericFailure(Request $request): JsonResponse
    {
        $msg = 'These credentials do not match our records.';

        return $this->authJsonValidationError($request, $msg, ['email' => [$msg]]);
    }
}
