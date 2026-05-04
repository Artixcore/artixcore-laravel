<?php

namespace App\Http\Requests\Admin;

use App\Models\HomepageSection;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateHomepageSectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var HomepageSection|null $section */
        $section = $this->route('homepage_section');

        return [
            'key' => [
                'sometimes',
                'string',
                'max:64',
                Rule::unique('homepage_sections', 'key')->ignore($section?->id),
            ],
            'title' => ['nullable', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:50000'],
            'badge_text' => ['nullable', 'string', 'max:255'],
            'button_text' => ['nullable', 'string', 'max:255'],
            'button_url' => ['nullable', 'string', 'max:2048', $this->urlRule()],
            'secondary_button_text' => ['nullable', 'string', 'max:255'],
            'secondary_button_url' => ['nullable', 'string', 'max:2048', $this->urlRule()],
            'image_path' => ['nullable', 'string', 'max:2048'],
            'is_enabled' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer', 'min:0', 'max:999999'],
            'settings' => ['nullable', 'array'],
            'settings_json' => ['nullable', 'string', 'max:100000'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v): void {
            $json = $this->input('settings_json');
            if (is_string($json) && $json !== '') {
                $decoded = json_decode($json, true);
                if (json_last_error() !== JSON_ERROR_NONE || ! is_array($decoded)) {
                    $v->errors()->add('settings_json', 'Invalid JSON.');
                }
            }
        });
    }

    /**
     * @return \Closure(string, mixed, \Closure): void
     */
    private function urlRule(): \Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail): void {
            if ($value === null || $value === '') {
                return;
            }
            if (! is_string($value)) {
                $fail('Invalid URL.');

                return;
            }
            if (preg_match('#^/#', $value)) {
                return;
            }
            if (filter_var($value, FILTER_VALIDATE_URL)) {
                return;
            }
            $fail('Must be an internal path starting with / or a full URL.');
        };
    }
}
