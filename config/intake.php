<?php

return [

    'per_minute' => (int) env('INTAKE_RATE_LIMIT_PER_MINUTE', 8),

    'per_day_per_ip' => (int) env('INTAKE_RATE_LIMIT_PER_DAY', 40),

    'geo_enabled' => env('INTAKE_GEO_ENABLED', false),

    /**
     * Driver: ip_api (free HTTP API) or ipinfo (HTTPS, token recommended).
     */
    'geo_driver' => env('INTAKE_GEO_DRIVER', 'ip_api'),

    'ipinfo_token' => env('IPINFO_TOKEN'),

];
