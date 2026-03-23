<?php

namespace App\Services\Tools\Handlers;

use App\Models\User;
use App\Services\Tools\Contracts\ToolHandlerInterface;
use App\Services\Tools\Network\SafeHttpFetcher;
use InvalidArgumentException;

class LinkSafetySummaryHandler implements ToolHandlerInterface
{
    public function __construct(
        private SafeHttpFetcher $fetcher,
        private PhishingSuspicionHandler $phishing
    ) {}

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

        $phish = $this->phishing->handle(['url' => $url], $user);

        $http = null;
        try {
            $http = $this->fetcher->fetch($url, 'HEAD');
        } catch (InvalidArgumentException $e) {
            $http = ['error' => $e->getMessage()];
        } catch (\Throwable $e) {
            $http = ['error' => $e->getMessage()];
        }

        return [
            'url' => $url,
            'url_heuristics' => $phish,
            'http_head' => is_array($http) && isset($http['status']) ? [
                'status' => $http['status'],
                'final_url' => $http['final_url'],
                'timing_ms' => $http['timing_ms'],
            ] : $http,
            'disclaimer' => 'Informational summary only; not malware scanning or a guarantee of safety.',
        ];
    }
}
