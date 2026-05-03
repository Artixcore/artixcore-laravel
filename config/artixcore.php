<?php

return [
    /*
    | Filament panel is disabled in favor of Blade /admin and /master.
    | Package may remain installed; no public panel when false.
    */
    'filament_panel_enabled' => (bool) env('FILAMENT_PANEL_ENABLED', false),

    'admin_ip_allowlist_bypass' => filter_var(env('ADMIN_IP_ALLOWLIST_BYPASS', false), FILTER_VALIDATE_BOOLEAN),

    'filament_legacy_redirect' => env('FILAMENT_LEGACY_REDIRECT', 'admin_login'), // admin_login | not_found

    'admin_login_per_minute' => max(1, (int) env('ADMIN_LOGIN_RATE_LIMIT', 5)),

    'master_login_per_minute' => max(1, (int) env('MASTER_LOGIN_RATE_LIMIT', 3)),
];
