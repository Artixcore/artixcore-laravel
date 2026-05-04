<?php

namespace App\Exceptions;

use App\Http\Support\AjaxRequestExpectations;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Throwable;

final class AjaxExceptionRenderer
{
    public static function register(Exceptions $exceptions): void
    {
        $exceptions->shouldRenderJsonWhen(function (Request $request, Throwable $e): bool {
            return AjaxRequestExpectations::prefersJsonResponse($request);
        });

        $exceptions->context(function (Throwable $e): array {
            $req = request();

            return array_filter([
                'route' => $req?->route()?->getName(),
                'path' => $req?->path(),
                'user_id' => $req?->user()?->getAuthIdentifier(),
                'ip' => $req?->ip(),
                'exception' => $e::class,
            ]);
        });

        $exceptions->renderable(function (ValidationException $e, Request $request): ?JsonResponse {
            if (! AjaxRequestExpectations::prefersJsonResponse($request)) {
                return null;
            }

            return response()->json([
                'ok' => false,
                'message' => __('Please check the form and try again.'),
                'errors' => $e->errors(),
            ], $e->status);
        });

        $exceptions->renderable(function (AuthenticationException $e, Request $request): ?JsonResponse {
            if (! AjaxRequestExpectations::prefersJsonResponse($request)) {
                return null;
            }

            return response()->json([
                'ok' => false,
                'message' => __('You are not allowed to perform this action.'),
            ], 401);
        });

        $exceptions->renderable(function (AccessDeniedHttpException $e, Request $request): ?JsonResponse {
            if (! AjaxRequestExpectations::prefersJsonResponse($request)) {
                return null;
            }

            return response()->json([
                'ok' => false,
                'message' => __('You are not allowed to perform this action.'),
            ], 403);
        });

        $exceptions->renderable(function (NotFoundHttpException $e, Request $request): ?JsonResponse {
            if (! AjaxRequestExpectations::prefersJsonResponse($request)) {
                return null;
            }

            return response()->json([
                'ok' => false,
                'message' => __('Not found.'),
            ], 404);
        });

        $exceptions->renderable(function (TooManyRequestsHttpException $e, Request $request): ?JsonResponse {
            if (! AjaxRequestExpectations::prefersJsonResponse($request)) {
                return null;
            }

            return response()->json([
                'ok' => false,
                'message' => __('Too many attempts. Please wait and try again.'),
            ], 429, $e->getHeaders());
        });

        $exceptions->renderable(function (QueryException $e, Request $request): ?JsonResponse {
            if (! AjaxRequestExpectations::prefersJsonResponse($request)) {
                return null;
            }

            report($e);
            Log::error('database.query_exception', [
                'exception' => $e::class,
                'message' => $e->getMessage(),
                'route' => $request->route()?->getName(),
                'path' => $request->path(),
            ]);

            return response()->json([
                'ok' => false,
                'message' => __('Something went wrong. Please try again.'),
            ], 500);
        });

        $exceptions->renderable(function (Throwable $e, Request $request): ?JsonResponse {
            if (! AjaxRequestExpectations::prefersJsonResponse($request)) {
                return null;
            }

            if ($e instanceof HttpResponseException) {
                return null;
            }

            if ($e instanceof ValidationException
                || $e instanceof AuthenticationException
                || $e instanceof QueryException
            ) {
                return null;
            }

            if ($e instanceof AccessDeniedHttpException
                || $e instanceof NotFoundHttpException
                || $e instanceof TooManyRequestsHttpException
            ) {
                return null;
            }

            if ($e instanceof HttpExceptionInterface) {
                $status = $e->getStatusCode();

                if ($status >= 500) {
                    report($e);
                    Log::error('http_exception', [
                        'exception' => $e::class,
                        'status' => $status,
                        'route' => $request->route()?->getName(),
                        'path' => $request->path(),
                    ]);
                    $safe = __('Something went wrong. Please try again.');
                } else {
                    $safe = __('Something went wrong. Please try again.');
                }

                return response()->json([
                    'ok' => false,
                    'message' => $safe,
                ], $status, $e->getHeaders());
            }

            report($e);
            Log::error('unhandled_exception', [
                'exception' => $e::class,
                'message' => $e->getMessage(),
                'route' => $request->route()?->getName(),
                'path' => $request->path(),
            ]);

            return response()->json([
                'ok' => false,
                'message' => __('Something went wrong. Please try again.'),
            ], 500);
        });
    }
}
