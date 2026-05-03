<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Driver: turnstile | recaptcha_v2
    |--------------------------------------------------------------------------
    */
    'driver' => env('CAPTCHA_DRIVER', 'turnstile'),

    /*
    | When true, only in local + testing environments, verification is skipped.
    | Never enable in production.
    */
    'bypass' => env('CAPTCHA_BYPASS', false),

    'turnstile' => [
        'site_key' => env('TURNSTILE_SITE_KEY', ''),
        'secret_key' => env('TURNSTILE_SECRET_KEY', ''),
        'verify_url' => 'https://challenges.cloudflare.com/turnstile/v0/siteverify',
    ],

    'recaptcha_v2' => [
        'site_key' => env('RECAPTCHA_SITE_KEY', ''),
        'secret_key' => env('RECAPTCHA_SECRET_KEY', ''),
        'verify_url' => 'https://www.google.com/recaptcha/api/siteverify',
    ],
];
