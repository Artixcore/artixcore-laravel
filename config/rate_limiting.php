<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Web — soft cap for public marketing pages (per IP)
    |--------------------------------------------------------------------------
    */
    'public_web_per_minute' => (int) env('RATE_LIMIT_PUBLIC_PER_MINUTE', 120),

    /*
    |--------------------------------------------------------------------------
    | Lead / contact form submissions (per IP and per IP+email)
    |--------------------------------------------------------------------------
    */
    'forms_per_minute' => (int) env('RATE_LIMIT_FORMS_PER_MINUTE', 5),

    /*
    |--------------------------------------------------------------------------
    | Session login (Blade /admin)
    |--------------------------------------------------------------------------
    */
    'login_per_minute' => (int) env('RATE_LIMIT_LOGIN_PER_MINUTE', 5),

    /*
    |--------------------------------------------------------------------------
    | AI chat (minute). PlatformSecuritySetting may override in AppServiceProvider.
    |--------------------------------------------------------------------------
    */
    'ai_chat_per_minute' => (int) env('RATE_LIMIT_AI_CHAT_PER_MINUTE', 10),

    /*
    |--------------------------------------------------------------------------
    | API baseline — guests (per IP) vs authenticated Sanctum users (per user id)
    |--------------------------------------------------------------------------
    */
    'api_guest_per_minute' => (int) env('RATE_LIMIT_API_GUEST_PER_MINUTE', 120),
    'api_authenticated_per_minute' => (int) env('RATE_LIMIT_API_AUTHENTICATED_PER_MINUTE', 300),

    /*
    |--------------------------------------------------------------------------
    | Analytics event ingestion
    |--------------------------------------------------------------------------
    */
    'analytics_per_minute' => (int) env('RATE_LIMIT_ANALYTICS_PER_MINUTE', 60),

    /*
    |--------------------------------------------------------------------------
    | Minimum seconds between lead form load (session) and submit (anti-bot)
    |--------------------------------------------------------------------------
    */
    'form_timing_min_seconds' => (int) env('FORM_TIMING_MIN_SECONDS', 2),

    /*
    | Skip minimum delay between lead form load and submit (local/testing only).
    */
    'form_timing_bypass' => filter_var(env('FORM_TIMING_BYPASS', false), FILTER_VALIDATE_BOOLEAN),

];
