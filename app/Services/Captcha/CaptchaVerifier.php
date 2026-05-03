<?php

namespace App\Services\Captcha;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CaptchaVerifier
{
    public function __construct(
        private TurnstileVerifier $turnstileVerifier,
    ) {}

    public function verify(Request $request): bool
    {
        if ($this->allowsBypass()) {
            return true;
        }

        $driver = (string) config('captcha.driver', 'turnstile');
        $token = $this->resolveToken($request);

        if ($token === null || $token === '') {
            return false;
        }

        return match ($driver) {
            'turnstile' => $this->verifyTurnstile($token, $request->ip()),
            'recaptcha_v2' => $this->verifyRecaptchaV2($token, $request->ip()),
            default => false,
        };
    }

    public function allowsBypass(): bool
    {
        if (! config('captcha.bypass')) {
            return false;
        }

        return app()->environment(['local', 'testing']);
    }

    private function resolveToken(Request $request): ?string
    {
        $captcha = $request->input('captcha');
        if (is_string($captcha) && $captcha !== '') {
            return $captcha;
        }

        $turnstile = $request->input('cf-turnstile-response');
        if (is_string($turnstile) && $turnstile !== '') {
            return $turnstile;
        }

        $recaptcha = $request->input('g-recaptcha-response');
        if (is_string($recaptcha) && $recaptcha !== '') {
            return $recaptcha;
        }

        return null;
    }

    private function verifyTurnstile(string $token, ?string $remoteIp): bool
    {
        return $this->turnstileVerifier->verify($token, $remoteIp);
    }

    private function verifyRecaptchaV2(string $token, ?string $remoteIp): bool
    {
        $secret = (string) config('captcha.recaptcha_v2.secret_key', '');
        if ($secret === '') {
            Log::warning('reCAPTCHA secret key not configured.');

            return false;
        }

        try {
            $response = Http::asForm()
                ->timeout(10)
                ->post((string) config('captcha.recaptcha_v2.verify_url'), [
                    'secret' => $secret,
                    'response' => $token,
                    'remoteip' => $remoteIp,
                ]);
        } catch (\Throwable $e) {
            report($e);

            return false;
        }

        if (! $response->successful()) {
            return false;
        }

        $json = $response->json();

        return is_array($json) && ! empty($json['success']);
    }
}
