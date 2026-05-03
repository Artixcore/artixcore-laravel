<?php

namespace App\Http\Requests\Api\V1;

use App\Http\Requests\Concerns\ValidatesTurnstileCaptcha;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class StoreContactRequest extends FormRequest
{
    use ValidatesTurnstileCaptcha;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return array_merge([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'message' => ['required', 'string', 'max:5000'],
        ], $this->captchaFieldRules());
    }

    public function withValidator(Validator $validator): void
    {
        $this->registerCaptchaValidator($validator);
    }
}
