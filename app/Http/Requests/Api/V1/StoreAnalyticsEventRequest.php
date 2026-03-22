<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreAnalyticsEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'event_type' => 'required|string|max:120',
            'session_id' => 'sometimes|nullable|string|max:120',
            'payload' => 'sometimes|array',
        ];
    }
}
