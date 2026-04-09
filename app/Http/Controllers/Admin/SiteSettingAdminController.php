<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Media\CreateMediaAssetFromUpload;
use App\Http\Controllers\Controller;
use App\Models\MediaAsset;
use App\Models\SiteSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SiteSettingAdminController extends Controller
{
    public function edit(): View
    {
        $this->authorize('update', SiteSetting::instance());

        return view('admin.site-settings.edit', [
            'settings' => SiteSetting::instance()->load(['logoMedia', 'faviconMedia', 'ogDefaultMedia']),
        ]);
    }

    public function update(Request $request): JsonResponse|RedirectResponse
    {
        $this->authorize('update', SiteSetting::instance());

        $data = $request->validate([
            'site_name' => ['nullable', 'string', 'max:255'],
            'default_meta_title' => ['nullable', 'string', 'max:255'],
            'default_meta_description' => ['nullable', 'string', 'max:2000'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'social_links_json' => ['nullable', 'string', 'max:10000'],
            'logo' => ['nullable', 'image', 'max:10240'],
            'favicon' => ['nullable', 'file', 'max:10240'],
            'og_image' => ['nullable', 'image', 'max:10240'],
        ]);

        if ($request->hasFile('logo') || $request->hasFile('favicon') || $request->hasFile('og_image')) {
            $this->authorize('create', MediaAsset::class);
        }

        $settings = SiteSetting::instance();
        $settings->fill([
            'site_name' => $data['site_name'] ?? null,
            'default_meta_title' => $data['default_meta_title'] ?? null,
            'default_meta_description' => $data['default_meta_description'] ?? null,
            'contact_email' => $data['contact_email'] ?? null,
        ]);

        if (! empty($data['social_links_json'])) {
            $decoded = json_decode($data['social_links_json'], true);
            $settings->social_links = is_array($decoded) ? $decoded : $settings->social_links;
        }

        $action = app(CreateMediaAssetFromUpload::class);
        $user = $request->user();

        if ($request->hasFile('logo')) {
            $asset = $action->execute($request->file('logo'), $user, 'Site logo');
            $settings->logo_media_id = $asset->id;
        }
        if ($request->hasFile('favicon')) {
            $asset = $action->execute($request->file('favicon'), $user, 'Site favicon');
            $settings->favicon_media_id = $asset->id;
        }
        if ($request->hasFile('og_image')) {
            $asset = $action->execute($request->file('og_image'), $user, 'Default Open Graph image');
            $settings->og_default_media_id = $asset->id;
        }

        $settings->save();
        $settings->load(['logoMedia', 'faviconMedia', 'ogDefaultMedia']);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Site settings saved.',
                'previews' => [
                    'logo' => $settings->logoMedia?->absoluteUrl(),
                    'favicon' => $settings->faviconMedia?->absoluteUrl(),
                    'og_image' => $settings->ogDefaultMedia?->absoluteUrl(),
                ],
            ]);
        }

        return redirect()->back()->with('status', 'Site settings saved.');
    }
}
