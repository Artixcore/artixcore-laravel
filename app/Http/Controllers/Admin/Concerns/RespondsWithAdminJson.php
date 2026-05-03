<?php

namespace App\Http\Controllers\Admin\Concerns;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

trait RespondsWithAdminJson
{
    protected function adminRespond(Request $request, string $message, ?string $redirect = null, array $extra = [], int $status = 200): JsonResponse|RedirectResponse
    {
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(array_merge([
                'success' => $status < 400,
                'message' => $message,
                'redirect' => $redirect,
            ], $extra), $status);
        }

        if ($redirect !== null) {
            return redirect()->to($redirect)->with('status', $message);
        }

        return back()->with('status', $message);
    }
}
