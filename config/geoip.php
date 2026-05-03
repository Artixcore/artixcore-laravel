<?php

return [

    'enabled' => filter_var(env('GEOIP_ENABLED', false), FILTER_VALIDATE_BOOLEAN)
        || filter_var(env('INTAKE_GEO_ENABLED', false), FILTER_VALIDATE_BOOLEAN),

    'driver' => env('INTAKE_GEO_DRIVER', 'ip_api'),

    'ipinfo_token' => env('IPINFO_TOKEN'),

    'cache_ttl_seconds' => (int) env('GEOIP_CACHE_TTL', 86400),

];
