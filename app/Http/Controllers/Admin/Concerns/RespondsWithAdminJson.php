<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Http\Controllers\Concerns\RespondsWithJson;
use App\Http\Support\AjaxRequestExpectations;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

trait RespondsWithAdminJson
{
    use RespondsWithJson;

    protected function adminRespond(Request $request, string $message, ?string $redirect = null, array $data = [], int $status = 200): JsonResponse|RedirectResponse
    {
        if (AjaxRequestExpectations::prefersJsonResponse($request)) {
            return response()->json([
                'ok' => $status < 400,
                'message' => $message,
                'redirect' => $redirect,
                'data' => $data,
            ], $status);
        }

        if ($redirect !== null) {
            return redirect()->to($redirect)->with('status', $message);
        }

        return back()->with('status', $message);
    }
}
