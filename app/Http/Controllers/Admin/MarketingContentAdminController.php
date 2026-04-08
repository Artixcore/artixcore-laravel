<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MarketingContentAdminController extends Controller
{
    public function edit(): View
    {
        $this->authorize('update', SiteSetting::instance());

        $s = SiteSetting::instance();

        return view('admin.marketing-content.edit', [
            'homepageJson' => json_encode($s->homepage_content ?? new \stdClass, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
            'aboutJson' => json_encode($s->about_content ?? new \stdClass, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
            'servicesPageJson' => json_encode($s->services_page_content ?? new \stdClass, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
        ]);
    }

    public function update(Request $request): JsonResponse|RedirectResponse
    {
        $this->authorize('update', SiteSetting::instance());

        $data = $request->validate([
            'homepage_content_json' => ['required', 'string', 'max:100000'],
            'about_content_json' => ['required', 'string', 'max:100000'],
            'services_page_content_json' => ['required', 'string', 'max:100000'],
        ]);

        $home = json_decode($data['homepage_content_json'], true);
        if (json_last_error() !== JSON_ERROR_NONE || ! is_array($home)) {
            return $request->wantsJson()
                ? response()->json(['success' => false, 'message' => 'Homepage JSON is invalid.'], 422)
                : back()->withErrors(['homepage_content_json' => 'Invalid JSON'])->withInput();
        }

        $about = json_decode($data['about_content_json'], true);
        if (json_last_error() !== JSON_ERROR_NONE || ! is_array($about)) {
            return $request->wantsJson()
                ? response()->json(['success' => false, 'message' => 'About JSON is invalid.'], 422)
                : back()->withErrors(['about_content_json' => 'Invalid JSON'])->withInput();
        }

        $servicesPage = json_decode($data['services_page_content_json'], true);
        if (json_last_error() !== JSON_ERROR_NONE || ! is_array($servicesPage)) {
            return $request->wantsJson()
                ? response()->json(['success' => false, 'message' => 'Services page JSON is invalid.'], 422)
                : back()->withErrors(['services_page_content_json' => 'Invalid JSON'])->withInput();
        }

        $settings = SiteSetting::instance();
        $settings->homepage_content = $home;
        $settings->about_content = $about;
        $settings->services_page_content = $servicesPage;
        $settings->save();

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Marketing content saved.']);
        }

        return redirect()->back()->with('status', 'Marketing content saved.');
    }
}
