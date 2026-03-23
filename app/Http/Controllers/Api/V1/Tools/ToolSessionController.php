<?php

namespace App\Http\Controllers\Api\V1\Tools;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ToolSessionController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'data' => [
                'authenticated' => $user !== null,
                'ad_free' => $user !== null,
                'aid' => $user?->aid,
            ],
        ]);
    }
}
