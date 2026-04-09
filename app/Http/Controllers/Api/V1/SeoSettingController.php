<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\SyncSeoSettingsRequest;
use App\Models\SiteSetting;
use App\Services\SeoSettingsService;
use Illuminate\Http\JsonResponse;

class SeoSettingController extends Controller
{
    public function show(SeoSettingsService $seo): JsonResponse
    {
        $site = SiteSetting::instance()->loadMissing('ogDefaultMedia');

        return response()->json([
            'data' => $seo->getResolvedPublicPayload($site),
        ]);
    }

    public function update(SyncSeoSettingsRequest $request, SeoSettingsService $seo): JsonResponse
    {
        $this->authorize('update', SiteSetting::instance());

        $seo->syncFromValidated($request->validatedSeoPayload());

        return response()->json([
            'data' => [
                'success' => true,
                'message' => 'SEO settings saved.',
            ],
        ]);
    }
}
