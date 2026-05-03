<?php

namespace App\Http\Requests\Web;

use App\Http\Requests\Concerns\ValidatesTurnstileCaptcha;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreIntakeLeadRequest extends FormRequest
{
    use ValidatesTurnstileCaptcha;

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $v = $this->input('address_line_2');
        if (is_string($v) && trim($v) !== '') {
            throw new HttpResponseException(response()->json([
                'data' => [
                    'conversation_public_id' => '00000000-0000-0000-0000-000000000099',
                    'opening_message' => 'Thank you.',
                    'agent_slug' => 'noop',
                ],
            ], 200));
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return array_merge([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:64'],
            'visitor_token' => ['required', 'string', 'min:16', 'max:128', 'regex:/^[a-zA-Z0-9_-]+$/'],
            'client_context' => ['nullable', 'array'],
            'client_context.timezone' => ['nullable', 'string', 'max:64'],
            'client_context.locale' => ['nullable', 'string', 'max:32'],
            'address_line_2' => ['nullable', 'string', 'max:0'],
        ], $this->captchaFieldRules());
    }

    public function withValidator(Validator $validator): void
    {
        $this->registerCaptchaValidator($validator);
    }
}
