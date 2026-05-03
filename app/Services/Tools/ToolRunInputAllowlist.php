<?php

namespace App\Services\Tools;

/**
 * Allowlisted JSON keys per micro-tool slug — prevents arbitrary mass input reaching handlers.
 */
final class ToolRunInputAllowlist
{
    /**
     * @return array<int, string>
     */
    public function allowedKeysForSlug(string $slug): array
    {
        return match ($slug) {
            'dns-lookup' => ['hostname', 'types'],
            'ssl-checker' => ['host', 'port'],
            'keyword-density' => ['text'],
            'ip-hosting-info' => ['query'],
            'email-security-records', 'public-exposure-snapshot' => ['domain'],
            'uptime-check', 'meta-tag-checker', 'social-preview-check', 'website-audit-basic',
            'speed-snapshot', 'website-technology', 'mobile-friendly-hints', 'schema-markup-check',
            'security-headers', 'sitemap-robots-check', 'link-safety-summary', 'phishing-suspicion' => ['url'],
            default => [],
        };
    }

    /**
     * @param  array<string, mixed>  $input
     * @return array<string, mixed>
     */
    public function filter(string $slug, array $input): array
    {
        $allowed = $this->allowedKeysForSlug($slug);

        if ($allowed === []) {
            return [];
        }

        $out = [];
        foreach ($allowed as $key) {
            if (! array_key_exists($key, $input)) {
                continue;
            }
            $out[$key] = $input[$key];
        }

        return $out;
    }
}
