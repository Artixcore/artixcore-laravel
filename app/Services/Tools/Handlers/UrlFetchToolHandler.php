<?php

namespace App\Services\Tools\Handlers;

use App\Models\User;
use App\Services\Tools\Contracts\ToolHandlerInterface;
use App\Services\Tools\Network\SafeHttpFetcher;
use InvalidArgumentException;

/**
 * Shared URL-based inspections (meta, audit, speed, tech, mobile hints, schema, social).
 */
abstract class UrlFetchToolHandler implements ToolHandlerInterface
{
    public function __construct(protected SafeHttpFetcher $fetcher) {}

    /**
     * @param  array<string, mixed>  $input
     */
    abstract protected function analyze(string $url, string $html, array $fetchMeta, ?User $user): array;

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

        $result = $this->fetcher->fetch($url, 'GET');
        $html = $result['body'];
        $fetchMeta = [
            'status' => $result['status'],
            'final_url' => $result['final_url'],
            'timing_ms' => $result['timing_ms'],
            'content_length_bytes' => strlen($html),
        ];

        return $this->analyze($url, $html, $fetchMeta, $user);
    }
}
