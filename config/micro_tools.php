<?php

return [
    'guest_per_minute' => (int) env('MICRO_TOOLS_GUEST_PER_MINUTE', 30),
    'auth_per_minute' => (int) env('MICRO_TOOLS_AUTH_PER_MINUTE', 120),
    'http_timeout_seconds' => (int) env('MICRO_TOOLS_HTTP_TIMEOUT', 10),
    'http_max_body_bytes' => (int) env('MICRO_TOOLS_HTTP_MAX_BYTES', 2_000_000),
];
