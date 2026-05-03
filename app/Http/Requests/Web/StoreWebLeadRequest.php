<?php

namespace App\Http\Requests\Web;

use App\Models\Lead;
use App\Services\Captcha\CaptchaVerifier;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
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
        /** @var CaptchaVerifier $verifier */
        $verifier = app(CaptchaVerifier::class);
        $bypass = $verifier->allowsBypass();
        $driver = (string) config('captcha.driver', 'turnstile');

        $rules = [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc,dns', 'max:190'],
            'phone' => ['nullable', 'string', 'max:40'],
            'service_type' => ['required', 'string', 'max:120', Rule::in(Lead::SERVICE_TYPES)],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
            'source' => ['nullable', 'string', 'max:64'],
            'website' => ['prohibited'],
            'captcha' => ['nullable', 'string', 'max:2000'],
        ];

        if (! $bypass) {
            if ($driver === 'turnstile') {
                $rules['cf-turnstile-response'] = ['required', 'string', 'max:2000'];
            } else {
                $rules['cf-turnstile-response'] = ['nullable', 'string', 'max:2000'];
            }
            if ($driver === 'recaptcha_v2') {
                $rules['g-recaptcha-response'] = ['required', 'string', 'max:2000'];
            } else {
                $rules['g-recaptcha-response'] = ['nullable', 'string', 'max:2000'];
            }
        } else {
            $rules['cf-turnstile-response'] = ['nullable', 'string', 'max:2000'];
            $rules['g-recaptcha-response'] = ['nullable', 'string', 'max:2000'];
        }

        return $rules;
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
                    __('Captcha verification failed. Please try again.')
                );
            }
        });
    }

    protected function failedValidation(Validator $validator): void
    {
        if ($this->expectsJson()) {
            $errors = $validator->errors();
            $onlyCaptcha = count($errors->keys()) === 1 && $errors->has('captcha');
            $message = $onlyCaptcha
                ? __('Captcha verification failed. Please try again.')
                : __('Please check the form and try again.');

            throw new HttpResponseException(response()->json([
                'ok' => false,
                'message' => $message,
                'errors' => $errors,
            ], 422));
        }

        parent::failedValidation($validator);
    }
}
