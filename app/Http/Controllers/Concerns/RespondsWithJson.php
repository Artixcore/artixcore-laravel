<?php

namespace App\Http\Controllers\Concerns;

use App\Http\Support\AjaxRequestExpectations;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

trait RespondsWithJson
{
    protected function wantsJsonResponse(Request $request): bool
    {
        return AjaxRequestExpectations::prefersJsonResponse($request);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function successResponse(
        string $message,
        array $data = [],
        ?string $redirect = null,
        int $status = 200,
    ): JsonResponse {
        return response()->json([
            'ok' => true,
            'message' => $message,
            'redirect' => $redirect,
            'data' => $data,
        ], $status);
    }

    /**
     * @param  array<string, array<int, string>>  $errors
     */
    protected function validationErrorResponse(
        array $errors,
        ?string $message = null,
    ): JsonResponse {
        return response()->json([
            'ok' => false,
            'message' => $message ?? __('Please check the form and try again.'),
            'errors' => $errors,
        ], 422);
    }

    /**
     * @param  array<string, array<int, string>>  $errors
     */
    protected function errorResponse(
        string $message,
        int $status = 500,
        array $errors = [],
    ): JsonResponse {
        $payload = [
            'ok' => false,
            'message' => $message,
        ];
        if ($errors !== []) {
            $payload['errors'] = $errors;
        }

        return response()->json($payload, $status);
    }
}
