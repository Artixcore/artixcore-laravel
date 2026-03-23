<?php

namespace App\Services\Tools\Handlers;

use App\Models\User;
use App\Services\Tools\Contracts\ToolHandlerInterface;
use App\Services\Tools\Network\SafeHttpFetcher;
use InvalidArgumentException;

class UptimeCheckHandler implements ToolHandlerInterface
{
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

        try {
            $result = $this->fetcher->fetch($url, 'GET');
        } catch (InvalidArgumentException $e) {
            throw $e;
        } catch (\Throwable $e) {
            return [
                'url' => $url,
                'reachable' => false,
                'status' => null,
                'timing_ms' => null,
                'error' => $e->getMessage(),
            ];
        }

        $status = $result['status'];

        return [
            'url' => $url,
            'final_url' => $result['final_url'],
            'reachable' => $status !== null && $status >= 200 && $status < 600,
            'status' => $status,
            'timing_ms' => $result['timing_ms'],
            'content_length' => strlen($result['body']),
        ];
    }
}
