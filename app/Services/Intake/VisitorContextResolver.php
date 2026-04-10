<?php

namespace App\Services\Intake;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Throwable;

class VisitorContextResolver
{
    /**
     * @param  array{timezone?: string, locale?: string}|null  $clientHints
     * @return array<string, mixed>
     */
    public function resolve(Request $request, ?array $clientHints): array
    {
        $ip = $request->ip() ?? '';
        $ipHash = hash('sha256', $ip.'|'.config('app.key'));

        $utm = array_filter([
            'source' => $request->query('utm_source'),
            'medium' => $request->query('utm_medium'),
            'campaign' => $request->query('utm_campaign'),
            'term' => $request->query('utm_term'),
            'content' => $request->query('utm_content'),
        ], fn ($v) => is_string($v) && $v !== '');

        $context = [
            'ip_hash' => $ipHash,
            'client_timezone' => isset($clientHints['timezone']) && is_string($clientHints['timezone'])
                ? mb_substr($clientHints['timezone'], 0, 64)
                : null,
            'locale' => isset($clientHints['locale']) && is_string($clientHints['locale'])
                ? mb_substr($clientHints['locale'], 0, 32)
                : null,
            'source_referrer' => $this->truncateReferrer($request->header('Referer')),
            'utm' => $utm === [] ? null : $utm,
            'captured_at' => now()->toIso8601String(),
            'geo_provider' => null,
            'country' => null,
            'country_code' => null,
            'region' => null,
            'city' => null,
            'timezone' => null,
        ];

        if (! config('intake.geo_enabled', false) || $ip === '' || $this->isNonPublicIp($ip)) {
            if ($context['timezone'] === null && $context['client_timezone'] !== null) {
                $context['timezone'] = $context['client_timezone'];
            }

            return $context;
        }

        $geo = $this->lookupGeo($ip);
        if ($geo !== null) {
            $context = array_merge($context, $geo);
        }

        if ($context['timezone'] === null && $context['client_timezone'] !== null) {
            $context['timezone'] = $context['client_timezone'];
        }

        return $context;
    }

    private function truncateReferrer(?string $referer): ?string
    {
        if ($referer === null || $referer === '') {
            return null;
        }

        return mb_substr($referer, 0, 2048);
    }

    private function isNonPublicIp(string $ip): bool
    {
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false;
    }

    /**
     * @return ?array<string, mixed>
     */
    private function lookupGeo(string $ip): ?array
    {
        $cacheKey = 'intake.geo.'.hash('sha256', $ip.config('app.key'));
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $driver = config('intake.geo_driver', 'ip_api');

        try {
            $data = $driver === 'ipinfo'
                ? $this->fetchIpInfo($ip)
                : $this->fetchIpApi($ip);
        } catch (Throwable) {
            $data = null;
        }

        Cache::put($cacheKey, $data, now()->addDay());

        return $data;
    }

    /**
     * @return ?array<string, mixed>
     */
    private function fetchIpApi(string $ip): ?array
    {
        $response = Http::timeout(2)
            ->get('http://ip-api.com/json/'.$ip, [
                'fields' => 'status,message,country,countryCode,regionName,city,timezone,query',
            ]);

        if (! $response->ok()) {
            return null;
        }

        $j = $response->json();
        if (! is_array($j) || ($j['status'] ?? '') !== 'success') {
            return null;
        }

        return [
            'country' => $j['country'] ?? null,
            'country_code' => $j['countryCode'] ?? null,
            'region' => $j['regionName'] ?? null,
            'city' => $j['city'] ?? null,
            'timezone' => $j['timezone'] ?? null,
            'geo_provider' => 'ip_api',
        ];
    }

    /**
     * @return ?array<string, mixed>
     */
    private function fetchIpInfo(string $ip): ?array
    {
        $token = config('intake.ipinfo_token');
        $url = 'https://ipinfo.io/'.$ip.'/json';
        if (is_string($token) && $token !== '') {
            $url .= '?token='.$token;
        }

        $response = Http::timeout(2)->get($url);
        if (! $response->ok()) {
            return null;
        }

        $j = $response->json();
        if (! is_array($j)) {
            return null;
        }

        $loc = null;
        if (isset($j['loc']) && is_string($j['loc'])) {
            $loc = $j['loc'];
        }

        return [
            'country' => $j['country'] ?? null,
            'country_code' => $j['country'] ?? null,
            'region' => $j['region'] ?? null,
            'city' => $j['city'] ?? null,
            'timezone' => $j['timezone'] ?? null,
            'geo_provider' => 'ipinfo',
            'loc_hint' => $loc,
        ];
    }
}
