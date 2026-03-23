<?php

namespace App\Services\Tools\Handlers;

use App\Models\User;
use App\Services\Tools\Contracts\ToolHandlerInterface;
use App\Services\Tools\Network\SafeHttpFetcher;
use InvalidArgumentException;

class SecurityHeadersHandler implements ToolHandlerInterface
{
    private const INTERESTING = [
        'strict-transport-security',
        'content-security-policy',
        'content-security-policy-report-only',
        'x-frame-options',
        'x-content-type-options',
        'referrer-policy',
        'permissions-policy',
        'cross-origin-opener-policy',
        'cross-origin-resource-policy',
    ];

    public function __construct(private SafeHttpFetcher $fetcher) {}

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

        $result = $this->fetcher->fetch($url, 'HEAD');
        $found = [];
        foreach (self::INTERESTING as $h) {
            if (isset($result['headers'][$h])) {
                $found[$h] = $result['headers'][$h];
            }
        }

        $missing = array_values(array_diff(self::INTERESTING, array_keys($found)));

        return [
            'url' => $url,
            'final_url' => $result['final_url'],
            'status' => $result['status'],
            'timing_ms' => $result['timing_ms'],
            'present' => $found,
            'missing' => $missing,
        ];
    }
}
