<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SyncSeoSettingsRequest;
use App\Http\Responses\AjaxFormEnvelope;
use App\Http\Support\AjaxRequestExpectations;
use App\Models\SiteSetting;
use App\Services\SeoSettingsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SeoSettingAdminController extends Controller
{
    public function edit(SeoSettingsService $seo): View
    {
        $this->authorize('update', SiteSetting::instance());

        return view('admin.seo-settings.edit', [
            'seo' => $seo->getForAdmin()['seo'],
        ]);
    }

    public function update(SyncSeoSettingsRequest $request, SeoSettingsService $seo): JsonResponse|RedirectResponse
    {
        $this->authorize('update', SiteSetting::instance());

        $seo->syncFromValidated($request->validatedSeoPayload());

        if (AjaxRequestExpectations::prefersJsonResponse($request)) {
            return AjaxFormEnvelope::success(__('SEO settings saved.'));
        }

        return redirect()->back()->with('status', 'SEO settings saved.');
    }
}
