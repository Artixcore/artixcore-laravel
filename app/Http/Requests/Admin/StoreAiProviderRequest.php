<?php

namespace App\Http\Requests\Admin;

use App\Models\AiProvider;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAiProviderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', AiProvider::class) ?? false;
    }

    protected function prepareForValidation(): void
    {
        foreach (['rate_limit_json', 'metadata_json'] as $key) {
            if ($this->input($key) === '') {
                $this->merge([$key => null]);
            }
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['nullable', 'string', 'max:255'],
            'driver' => ['required', 'string', Rule::in([
                AiProvider::DRIVER_OPENAI,
                AiProvider::DRIVER_GEMINI,
                AiProvider::DRIVER_GROK,
                AiProvider::DRIVER_CUSTOM,
            ])],
            'is_enabled' => ['sometimes', 'boolean'],
            'api_key' => ['nullable', 'string', 'max:8192'],
            'default_model' => ['nullable', 'string', 'max:255'],
            'base_url' => ['nullable', 'string', 'max:2048'],
            'timeout_seconds' => ['required', 'integer', 'min:5', 'max:600'],
            'priority' => ['required', 'integer', 'min:0', 'max:65535'],
            'max_output_tokens' => ['nullable', 'integer', 'min:1', 'max:128000'],
            'rate_limit_json' => ['nullable', 'string', 'max:10000', 'json'],
            'metadata_json' => ['nullable', 'string', 'max:10000', 'json'],
        ];
    }
}
