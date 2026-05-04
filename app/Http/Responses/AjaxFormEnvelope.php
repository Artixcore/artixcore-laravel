<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

final class AjaxFormEnvelope
{
    /**
     * @param  array<string, mixed>  $data
     */
    public static function success(string $message, array $data = [], ?string $redirect = null, int $status = 200): JsonResponse
    {
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
    public static function failure(string $message, int $status = 422, array $errors = []): JsonResponse
    {
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
