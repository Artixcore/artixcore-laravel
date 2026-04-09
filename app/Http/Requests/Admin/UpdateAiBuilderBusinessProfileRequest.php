<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAiBuilderBusinessProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('builder.access') ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'business_name' => ['nullable', 'string', 'max:255'],
            'brand_summary' => ['nullable', 'string', 'max:10000'],
            'business_type' => ['nullable', 'string', 'max:64'],
            'target_audience' => ['nullable', 'string', 'max:10000'],
            'main_services' => ['nullable', 'string', 'max:10000'],
            'unique_selling_points' => ['nullable', 'string', 'max:10000'],
            'tone_of_voice' => ['nullable', 'string', 'max:128'],
            'offer_details' => ['nullable', 'string', 'max:10000'],
            'location' => ['nullable', 'string', 'max:255'],
            'contact_details_json' => ['nullable', 'string', 'max:10000'],
            'preferred_cta_goal' => ['nullable', 'string', 'max:128'],
            'writing_style' => ['nullable', 'string', 'max:128'],
            'forbidden_topics' => ['nullable', 'string', 'max:10000'],
            'brand_colors_json' => ['nullable', 'string', 'max:10000'],
            'style_notes' => ['nullable', 'string', 'max:10000'],
        ];
    }
}
