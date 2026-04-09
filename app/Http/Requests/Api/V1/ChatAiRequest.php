<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class ChatAiRequest extends FormRequest
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
            'agent_slug' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9\-]+$/'],
            'message' => ['required', 'string', 'max:16000'],
            'conversation_public_id' => ['nullable', 'uuid'],
            'visitor_token' => ['required', 'string', 'min:16', 'max:128'],
        ];
    }
}
