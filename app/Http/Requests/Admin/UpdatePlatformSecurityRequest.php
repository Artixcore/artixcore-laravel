<?php

namespace App\Http\Requests\Admin;

use App\Models\PlatformSecuritySetting;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePlatformSecurityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', PlatformSecuritySetting::instance()) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'chat_rate_limit_per_minute' => ['required', 'integer', 'min:1', 'max:1000'],
            'chat_rate_limit_per_day' => ['required', 'integer', 'min:1', 'max:100000'],
            'builder_ai_rate_limit_per_minute' => ['required', 'integer', 'min:1', 'max:500'],
            'internal_notes' => ['nullable', 'string', 'max:10000'],
        ];
    }
}
