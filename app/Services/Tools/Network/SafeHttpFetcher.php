<?php

namespace App\Services\Tools\Network;

use Illuminate\Support\Facades\Http;

class SafeHttpFetcher
{
    public function __construct(private HostSafetyChecker $hosts) {}

    /**
     * @return array{status: int|null, headers: array<string, list<string>>, body: string, final_url: string, timing_ms: int}
     */
    public function fetch(string $url, string $method = 'GET'): array
    {
        $safeUrl = $this->hosts->assertSafeHttpUrl($url);
        $timeout = (int) config('micro_tools.http_timeout_seconds', 10);
        $maxBytes = (int) config('micro_tools.http_max_body_bytes', 2_000_000);

        $started = (int) (microtime(true) * 1000);

        $pending = Http::timeout($timeout)->withOptions(['allow_redirects' => ['max' => 5]]);
        $response = strtoupper($method) === 'HEAD'
            ? $pending->head($safeUrl)
            : $pending->get($safeUrl);

        $timingMs = (int) (microtime(true) * 1000) - $started;

        $body = $response->body();
        if (strlen($body) > $maxBytes) {
            $body = substr($body, 0, $maxBytes).'…[truncated]';
        }

        $headers = [];
        foreach ($response->headers() as $name => $values) {
            $headers[strtolower((string) $name)] = array_map('strval', (array) $values);
        }

        return [
            'status' => $response->status(),
            'headers' => $headers,
            'body' => $body,
            'final_url' => (string) $response->effectiveUri(),
            'timing_ms' => $timingMs,
        ];
    }
}
