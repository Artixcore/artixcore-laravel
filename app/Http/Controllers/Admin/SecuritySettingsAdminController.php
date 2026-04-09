<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\RespondsWithAdminJson;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdatePlatformSecurityRequest;
use App\Models\PlatformSecuritySetting;
use App\Services\Audit\ActivityLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SecuritySettingsAdminController extends Controller
{
    use RespondsWithAdminJson;

    public function __construct(private ActivityLogger $activityLog) {}

    public function edit(): View
    {
        $settings = PlatformSecuritySetting::instance();
        $this->authorize('view', $settings);

        return view('admin.security-settings.edit', [
            'security' => $settings,
            'appDebug' => config('app.debug'),
            'sessionLifetime' => config('session.lifetime'),
        ]);
    }

    public function update(UpdatePlatformSecurityRequest $request): JsonResponse|RedirectResponse
    {
        $settings = PlatformSecuritySetting::instance();
        $this->authorize('update', $settings);

        $validated = $request->validated();
        $settings->update($validated);

        $this->activityLog->log('security_settings.updated', $settings, $validated, $request);

        return $this->adminRespond($request, 'Security settings saved.', route('admin.security-settings.edit'));
    }
}
