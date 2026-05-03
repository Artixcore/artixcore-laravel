<?php

namespace App\Http\Requests\Concerns;

use App\Services\Captcha\CaptchaVerifier;
use Illuminate\Contracts\Validation\Validator;

trait ValidatesTurnstileCaptcha
{
    /**
     * Merge Turnstile/reCAPTCHA field rules (fail closed in production).
     *
     * @return array<string, mixed>
     */
    protected function captchaFieldRules(): array
    {
        /** @var CaptchaVerifier $verifier */
        $verifier = app(CaptchaVerifier::class);
        $bypass = $verifier->allowsBypass();
        $driver = (string) config('captcha.driver', 'turnstile');

        $rules = [
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

    protected function registerCaptchaValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            /** @var CaptchaVerifier $captcha */
            $captcha = app(CaptchaVerifier::class);
            if (! $captcha->verify($this)) {
                $msg = __('Captcha verification failed. Please try again.');
                $validator->errors()->add('captcha', $msg);
                if ((string) config('captcha.driver', 'turnstile') === 'turnstile') {
                    $validator->errors()->add('cf-turnstile-response', $msg);
                }
                if ((string) config('captcha.driver', 'turnstile') === 'recaptcha_v2') {
                    $validator->errors()->add('g-recaptcha-response', $msg);
                }
            }
        });
    }
}
