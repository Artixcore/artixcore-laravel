<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\RespondsWithAdminJson;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateAiBuilderBusinessProfileRequest;
use App\Models\AiBuilderBusinessProfile;
use App\Services\Audit\ActivityLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AiBuilderBusinessProfileAdminController extends Controller
{
    use RespondsWithAdminJson;

    public function __construct(private ActivityLogger $activityLogger) {}

    public function edit(): View
    {
        $profile = AiBuilderBusinessProfile::instance();

        return view('admin.ai-builder-context.edit', [
            'profile' => $profile,
        ]);
    }

    public function update(UpdateAiBuilderBusinessProfileRequest $request): JsonResponse|RedirectResponse
    {
        $profile = AiBuilderBusinessProfile::instance();

        $validated = $request->validated();
        $contact = null;
        if (! empty($validated['contact_details_json'])) {
            $decoded = json_decode($validated['contact_details_json'], true);
            $contact = is_array($decoded) ? $decoded : null;
        }
        $colors = null;
        if (! empty($validated['brand_colors_json'])) {
            $decoded = json_decode($validated['brand_colors_json'], true);
            $colors = is_array($decoded) ? $decoded : null;
        }

        $profile->update([
            'business_name' => $validated['business_name'] ?? null,
            'brand_summary' => $validated['brand_summary'] ?? null,
            'business_type' => $validated['business_type'] ?? null,
            'target_audience' => $validated['target_audience'] ?? null,
            'main_services' => $validated['main_services'] ?? null,
            'unique_selling_points' => $validated['unique_selling_points'] ?? null,
            'tone_of_voice' => $validated['tone_of_voice'] ?? null,
            'offer_details' => $validated['offer_details'] ?? null,
            'location' => $validated['location'] ?? null,
            'contact_details' => $contact,
            'preferred_cta_goal' => $validated['preferred_cta_goal'] ?? null,
            'writing_style' => $validated['writing_style'] ?? null,
            'forbidden_topics' => $validated['forbidden_topics'] ?? null,
            'brand_colors' => $colors,
            'style_notes' => $validated['style_notes'] ?? null,
        ]);

        $this->activityLogger->log('ai_builder_business_profile.updated', $profile, [], $request);

        return $this->adminRespond($request, 'AI builder context saved.', route('admin.ai-builder-context.edit'));
    }
}
