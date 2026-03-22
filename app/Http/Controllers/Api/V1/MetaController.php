<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Support\Content\PageBlockType;
use Illuminate\Http\JsonResponse;

class MetaController extends Controller
{
    public function blockTypes(): JsonResponse
    {
        return response()->json([
            'data' => [
                'block_types' => PageBlockType::toApiList(),
            ],
        ]);
    }
}
