<?php

namespace App\Http\Requests\Web;

use App\Models\Lead;
use App\Services\Captcha\CaptchaVerifier;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreWebLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190'],
            'phone' => ['nullable', 'string', 'max:40'],
            'service_type' => ['required', 'string', 'max:120', Rule::in(Lead::SERVICE_TYPES)],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
            'source' => ['nullable', 'string', 'max:64'],
            'website' => ['prohibited'],
            'captcha' => ['nullable', 'string', 'max:2000'],
            'cf-turnstile-response' => ['nullable', 'string', 'max:2000'],
            'g-recaptcha-response' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            /** @var CaptchaVerifier $captcha */
            $captcha = app(CaptchaVerifier::class);
            if (! $captcha->verify($this)) {
                $validator->errors()->add(
                    'captcha',
                    __('Human verification failed. Please try again.')
                );
            }
        });
    }
}
