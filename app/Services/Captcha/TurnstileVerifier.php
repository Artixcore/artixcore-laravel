<?php

namespace App\Services\Captcha;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TurnstileVerifier
{
    /**
     * Verify a Turnstile token with Cloudflare siteverify.
     *
     * @return bool True only when Cloudflare returns success true.
     */
    public function verify(string $token, ?string $remoteIp): bool
    {
        $secret = $this->secretKey();
        if ($secret === '') {
            if (app()->environment('production')) {
                Log::error('Turnstile secret key not configured; verification cannot proceed.');
            } else {
                Log::warning('Turnstile secret key not configured.');
            }

            return false;
        }

        $verifyUrl = (string) config('captcha.turnstile.verify_url', 'https://challenges.cloudflare.com/turnstile/v0/siteverify');

        try {
            $response = Http::asForm()
                ->timeout(10)
                ->post($verifyUrl, [
                    'secret' => $secret,
                    'response' => $token,
                    'remoteip' => $remoteIp,
                ]);
        } catch (\Throwable $e) {
            report($e);
            Log::warning('Turnstile siteverify HTTP request failed.', [
                'exception' => $e::class,
            ]);

            return false;
        }

        if (! $response->successful()) {
            Log::info('Turnstile siteverify non-success HTTP status.', [
                'status' => $response->status(),
            ]);

            return false;
        }

        $json = $response->json();
        if (! is_array($json)) {
            Log::info('Turnstile siteverify returned non-JSON body.');

            return false;
        }

        $success = ! empty($json['success']);
        if ($success) {
            return true;
        }

        $codes = $json['error-codes'] ?? null;
        if (is_array($codes) && $codes !== []) {
            Log::info('Turnstile verification failed.', ['error-codes' => $codes]);
        } else {
            Log::info('Turnstile verification failed.', ['success' => false]);
        }

        return false;
    }

    private function secretKey(): string
    {
        $fromServices = config('services.turnstile.secret_key');

        return is_string($fromServices) && $fromServices !== ''
            ? $fromServices
            : (string) config('captcha.turnstile.secret_key', '');
    }
}
