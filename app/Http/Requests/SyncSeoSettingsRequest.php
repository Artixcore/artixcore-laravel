<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SyncSeoSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (! $this->has('seo')) {
            $this->merge(['seo' => $this->all()]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $handle = ['nullable', 'string', 'max:100', 'regex:/^$|^@?[A-Za-z0-9_]+$/'];

        return [
            'seo' => ['required', 'array'],

            'seo.meta' => ['required', 'array'],
            'seo.meta.enabled' => ['boolean'],
            'seo.meta.pixel_id' => ['nullable', 'string', 'max:32', 'regex:/^$|^[0-9]{8,24}$/'],
            'seo.meta.pixel_id_active' => ['boolean'],
            'seo.meta.app_id' => ['nullable', 'string', 'max:32', 'regex:/^$|^[0-9]{5,20}$/'],
            'seo.meta.app_id_active' => ['boolean'],
            'seo.meta.og_title_override' => ['nullable', 'string', 'max:255'],
            'seo.meta.og_description_override' => ['nullable', 'string', 'max:2000'],
            'seo.meta.og_image_url' => ['nullable', 'string', 'max:2048', 'regex:/^$|^https?:\/\/.+/i'],

            'seo.google' => ['required', 'array'],
            'seo.google.enabled' => ['boolean'],
            'seo.google.ga4_measurement_id' => ['nullable', 'string', 'max:32', 'regex:/^$|^G-[A-Z0-9]+$/i'],
            'seo.google.ga4_measurement_id_active' => ['boolean'],
            'seo.google.gtm_container_id' => ['nullable', 'string', 'max:32', 'regex:/^$|^GTM-[A-Z0-9]+$/i'],
            'seo.google.gtm_container_id_active' => ['boolean'],
            'seo.google.adsense_publisher_id' => ['nullable', 'string', 'max:32', 'regex:/^$|^ca-pub-[0-9]+$/i'],
            'seo.google.adsense_publisher_id_active' => ['boolean'],
            'seo.google.search_console_verification' => ['nullable', 'string', 'max:255', 'regex:/^$|^[A-Za-z0-9_-]+$/'],
            'seo.google.search_console_verification_active' => ['boolean'],

            'seo.twitter' => ['required', 'array'],
            'seo.twitter.enabled' => ['boolean'],
            'seo.twitter.card_type' => ['nullable', 'string', Rule::in(['summary', 'summary_large_image'])],
            'seo.twitter.card_type_active' => ['boolean'],
            'seo.twitter.site_handle' => $handle,
            'seo.twitter.site_handle_active' => ['boolean'],
            'seo.twitter.creator_handle' => $handle,
            'seo.twitter.creator_handle_active' => ['boolean'],

            'seo.tiktok' => ['required', 'array'],
            'seo.tiktok.enabled' => ['boolean'],
            'seo.tiktok.pixel_id' => ['nullable', 'string', 'max:64', 'regex:/^$|^[A-Z0-9]+$/i'],
            'seo.tiktok.pixel_id_active' => ['boolean'],
            'seo.tiktok.event_settings' => ['nullable', 'string', 'max:10000'],

            'seo.additional' => ['required', 'array'],
            'seo.additional.enabled' => ['boolean'],
            'seo.additional.linkedin_partner_id' => ['nullable', 'string', 'max:32', 'regex:/^$|^[0-9]+$/'],
            'seo.additional.linkedin_partner_id_active' => ['boolean'],
            'seo.additional.pinterest_verification' => ['nullable', 'string', 'max:255', 'regex:/^$|^[A-Za-z0-9_-]+$/'],
            'seo.additional.pinterest_verification_active' => ['boolean'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function validatedSeoPayload(): array
    {
        /** @var array<string, mixed> $seo */
        $seo = $this->validated('seo');

        return $seo;
    }
}
