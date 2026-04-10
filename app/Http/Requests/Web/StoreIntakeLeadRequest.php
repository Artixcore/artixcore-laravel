<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class StoreIntakeLeadRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:64'],
            'visitor_token' => ['required', 'string', 'min:16', 'max:128', 'regex:/^[a-zA-Z0-9_-]+$/'],
            'client_context' => ['nullable', 'array'],
            'client_context.timezone' => ['nullable', 'string', 'max:64'],
            'client_context.locale' => ['nullable', 'string', 'max:32'],
            'address_line_2' => ['nullable', 'string', 'max:0'],
        ];
    }
}
