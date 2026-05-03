<?php

namespace App\Services\GeoIp;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Resolves approximate geo from IP. Never throws; returns null fields on failure.
 *
 * @return array{
 *     country: ?string,
 *     region: ?string,
 *     city: ?string,
 *     postal: ?string,
 *     latitude: ?float,
 *     longitude: ?float,
 *     raw_driver: ?string
 * }
 */
class GeoIpLookupService
{
    public function lookup(?string $ip): array
    {
        $empty = $this->emptyResult();

        if (! config('geoip.enabled')) {
            return $empty;
        }

        if ($ip === null || $ip === '' || $ip === '127.0.0.1' || str_starts_with($ip, '192.168.') || str_starts_with($ip, '10.')) {
            return $empty;
        }

        $cacheKey = 'geoip:'.hash('sha256', $ip);
        $ttl = max(60, (int) config('geoip.cache_ttl_seconds', 86400));

        try {
            return Cache::remember($cacheKey, $ttl, function () use ($ip, $empty): array {
                $driver = (string) config('geoip.driver', 'ip_api');

                if ($driver === 'ipinfo') {
                    return $this->lookupIpInfo($ip) ?? $empty;
                }

                return $this->lookupIpApi($ip) ?? $empty;
            });
        } catch (Throwable $e) {
            report($e);

            return $empty;
        }
    }

    /**
     * @return array<string, mixed>|null
     */
    private function lookupIpApi(string $ip): ?array
    {
        try {
            $response = Http::timeout(4)->get('http://ip-api.com/json/'.$ip, [
                'fields' => 'status,country,regionName,city,zip,lat,lon',
            ]);

            if (! $response->successful()) {
                return null;
            }

            $data = $response->json();
            if (! is_array($data) || ($data['status'] ?? '') !== 'success') {
                return null;
            }

            return [
                'country' => isset($data['country']) ? (string) $data['country'] : null,
                'region' => isset($data['regionName']) ? (string) $data['regionName'] : null,
                'city' => isset($data['city']) ? (string) $data['city'] : null,
                'postal' => isset($data['zip']) ? (string) $data['zip'] : null,
                'latitude' => isset($data['lat']) ? (float) $data['lat'] : null,
                'longitude' => isset($data['lon']) ? (float) $data['lon'] : null,
                'raw_driver' => 'ip_api',
            ];
        } catch (Throwable $e) {
            Log::debug('GeoIp ip-api lookup failed.', ['ip' => $ip, 'exception' => $e::class]);

            return null;
        }
    }

    /**
     * @return array<string, mixed>|null
     */
    private function lookupIpInfo(string $ip): ?array
    {
        $token = (string) config('geoip.ipinfo_token', '');
        if ($token === '') {
            return null;
        }

        try {
            $response = Http::timeout(4)->get('https://ipinfo.io/'.$ip.'/json', [
                'token' => $token,
            ]);

            if (! $response->successful()) {
                return null;
            }

            $data = $response->json();
            if (! is_array($data)) {
                return null;
            }

            $loc = isset($data['loc']) && is_string($data['loc']) ? explode(',', $data['loc']) : [];
            $lat = isset($loc[0]) ? (float) $loc[0] : null;
            $lon = isset($loc[1]) ? (float) $loc[1] : null;

            return [
                'country' => isset($data['country']) ? (string) $data['country'] : null,
                'region' => isset($data['region']) ? (string) $data['region'] : null,
                'city' => isset($data['city']) ? (string) $data['city'] : null,
                'postal' => isset($data['postal']) ? (string) $data['postal'] : null,
                'latitude' => $lat,
                'longitude' => $lon,
                'raw_driver' => 'ipinfo',
            ];
        } catch (Throwable $e) {
            Log::debug('GeoIp ipinfo lookup failed.', ['ip' => $ip, 'exception' => $e::class]);

            return null;
        }
    }

    /**
     * @return array{
     *     country: null,
     *     region: null,
     *     city: null,
     *     postal: null,
     *     latitude: null,
     *     longitude: null,
     *     raw_driver: null
     * }
     */
    private function emptyResult(): array
    {
        return [
            'country' => null,
            'region' => null,
            'city' => null,
            'postal' => null,
            'latitude' => null,
            'longitude' => null,
            'raw_driver' => null,
        ];
    }
}
