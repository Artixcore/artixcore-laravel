<?php

namespace App\Http\Requests\Web;

use App\Http\Requests\Concerns\ValidatesTurnstileCaptcha;
use App\Http\Support\AjaxRequestExpectations;
use App\Models\Lead;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class StoreWebLeadRequest extends FormRequest
{
    use ValidatesTurnstileCaptcha;

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('website')) {
            $this->throwDecoyLeadSuccess();
        }
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return array_merge([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc,dns', 'max:190'],
            'phone' => ['nullable', 'string', 'max:40'],
            'service_type' => ['required', 'string', 'max:120', Rule::in(Lead::SERVICE_TYPES)],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
            'source' => ['nullable', 'string', 'max:64'],
            'website' => ['nullable', 'string', 'max:191'],
        ], $this->captchaFieldRules());
    }

    public function withValidator($validator): void
    {
        $this->registerCaptchaValidator($validator);

        $validator->after(function ($validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            if ($this->shouldBypassFormTiming()) {
                return;
            }

            $loaded = session('lead_form_loaded_at');
            if ($loaded !== null && is_numeric($loaded)) {
                $elapsed = time() - (int) $loaded;
                if ($elapsed < (int) config('rate_limiting.form_timing_min_seconds', 2)) {
                    $this->throwDecoyLeadSuccess();
                }
            }
        });
    }

    private function shouldBypassFormTiming(): bool
    {
        if (app()->environment('testing')) {
            return true;
        }

        return (bool) config('rate_limiting.form_timing_bypass', false);
    }

    /**
     * @return never
     */
    private function throwDecoyLeadSuccess(): void
    {
        if (AjaxRequestExpectations::prefersJsonResponse($this)) {
            throw new HttpResponseException(response()->json([
                'ok' => true,
                'message' => __('Thank you for contacting Artixcore.'),
                'redirect' => null,
                'data' => [
                    'lead' => [
                        'name' => '',
                        'email' => '',
                    ],
                ],
            ], 200));
        }

        throw new HttpResponseException(
            redirect()
                ->route('lead.create')
                ->with('status', __('Thanks — we received your project request and will get back to you soon.'))
        );
    }

    protected function failedValidation(Validator $validator): void
    {
        if (AjaxRequestExpectations::prefersJsonResponse($this)) {
            $errors = $validator->errors();
            $captchaKeys = ['captcha', 'cf-turnstile-response', 'g-recaptcha-response'];
            $keys = $errors->keys();
            $onlyCaptcha = $keys !== [] && count(array_diff($keys, $captchaKeys)) === 0;
            $message = $onlyCaptcha
                ? __('Captcha verification failed. Please try again.')
                : __('Please check the form and try again.');

            throw new HttpResponseException(response()->json([
                'ok' => false,
                'message' => $message,
                'errors' => $errors->toArray(),
            ], 422));
        }

        parent::failedValidation($validator);
    }
}
