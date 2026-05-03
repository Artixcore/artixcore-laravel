<?php

namespace App\Http\Requests\Api\V1;

use App\Services\Captcha\CaptchaVerifier;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class ChatAiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Turnstile is required only when starting a new conversation (abuse vector).
     * Continuations reuse the authenticated conversation server-side.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var CaptchaVerifier $verifier */
        $verifier = app(CaptchaVerifier::class);
        $bypass = $verifier->allowsBypass();
        $driver = (string) config('captcha.driver', 'turnstile');
        $newThread = ! $this->filled('conversation_public_id');

        $captcha = [
            'captcha' => ['nullable', 'string', 'max:2000'],
            'cf-turnstile-response' => ['nullable', 'string', 'max:2000'],
            'g-recaptcha-response' => ['nullable', 'string', 'max:2000'],
        ];

        if (! $bypass && $newThread) {
            if ($driver === 'turnstile') {
                $captcha['cf-turnstile-response'] = ['required', 'string', 'max:2000'];
            }
            if ($driver === 'recaptcha_v2') {
                $captcha['g-recaptcha-response'] = ['required', 'string', 'max:2000'];
            }
        }

        return array_merge([
            'agent_slug' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9\-]+$/'],
            'message' => ['required', 'string', 'max:16000'],
            'conversation_public_id' => ['nullable', 'uuid'],
            'visitor_token' => ['required', 'string', 'min:16', 'max:128', 'regex:/^[a-zA-Z0-9_-]+$/'],
        ], $captcha);
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            if ($this->filled('conversation_public_id')) {
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
}
