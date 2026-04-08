<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
            'settings' => SiteSetting::instance(),
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
        ]);

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

        $settings->save();

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Site settings saved.']);
        }

        return redirect()->back()->with('status', 'Site settings saved.');
    }
}
