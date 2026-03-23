<?php

namespace App\Services\Tools\Handlers;

use App\Models\User;
use App\Services\Tools\Contracts\ToolHandlerInterface;
use App\Services\Tools\Network\HostSafetyChecker;
use InvalidArgumentException;

class PhishingSuspicionHandler implements ToolHandlerInterface
{
    public function __construct(private HostSafetyChecker $hosts) {}

    /**
     * @param  array<string, mixed>  $input
     * @return array<string, mixed>
     */
    public function handle(array $input, ?User $user): array
    {
        $url = isset($input['url']) ? trim((string) $input['url']) : '';
        if ($url === '') {
            throw new InvalidArgumentException('url is required.');
        }
        if (! str_contains($url, '://')) {
            $url = 'https://'.$url;
        }

        $parts = parse_url($url);
        if ($parts === false || ! isset($parts['host'])) {
            throw new InvalidArgumentException('Invalid URL.');
        }

        $host = strtolower((string) $parts['host']);
        $signals = [];
        $score = 0;

        if (preg_match('/\d{1,3}(?:\.\d{1,3}){3}/', $host)) {
            $signals[] = 'host_contains_ip_literal';
            $score += 2;
        }

        if (preg_match('/-{2,}/', $host)) {
            $signals[] = 'multiple_hyphens';
            $score += 1;
        }

        if (preg_match('/paypal|amazon|microsoft|google|apple|facebook/i', $host) && ! preg_match('/\.(paypal|amazon|microsoft|google|apple|facebook)\./i', $host)) {
            $signals[] = 'brand_like_substring_in_non_brand_host';
            $score += 3;
        }

        if (strlen($host) > 60) {
            $signals[] = 'very_long_hostname';
            $score += 1;
        }

        if (function_exists('idn_to_ascii')) {
            $puny = @idn_to_ascii($host, IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46);
            if ($puny !== false && str_contains($puny, 'xn--')) {
                $signals[] = 'punycode_hostname';
                $score += 1;
            }
        }

        $connectSafe = true;
        try {
            $this->hosts->assertPublicResolvableHost($host);
        } catch (InvalidArgumentException) {
            $connectSafe = false;
            $signals[] = 'host_not_publicly_resolvable_for_connect';
        }

        return [
            'url' => $url,
            'host' => $host,
            'signals' => $signals,
            'risk_score' => min(10, $score),
            'disclaimer' => 'Heuristic only; not a verdict. Investigate with official channels when in doubt.',
            'connect_would_be_blocked' => ! $connectSafe,
        ];
    }
}
