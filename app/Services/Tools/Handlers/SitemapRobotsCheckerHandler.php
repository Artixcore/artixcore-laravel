<?php

namespace App\Services\Tools\Handlers;

use App\Models\User;
use App\Services\Tools\Contracts\ToolHandlerInterface;
use App\Services\Tools\Network\SafeHttpFetcher;
use InvalidArgumentException;

class SitemapRobotsCheckerHandler implements ToolHandlerInterface
{
    public function __construct(
        private SafeHttpFetcher $fetcher
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

        $parts = parse_url($url);
        if ($parts === false || ! isset($parts['scheme'], $parts['host'])) {
            throw new InvalidArgumentException('Invalid URL.');
        }

        $origin = $parts['scheme'].'://'.$parts['host'].(isset($parts['port']) ? ':'.$parts['port'] : '');
        $robotsUrl = rtrim($origin, '/').'/robots.txt';

        $robots = null;
        $robotsError = null;
        try {
            $r = $this->fetcher->fetch($robotsUrl, 'GET');
            $robots = [
                'status' => $r['status'],
                'timing_ms' => $r['timing_ms'],
                'body_preview' => mb_substr($r['body'], 0, 8000),
            ];
        } catch (InvalidArgumentException $e) {
            $robotsError = $e->getMessage();
        } catch (\Throwable $e) {
            $robotsError = $e->getMessage();
        }

        $sitemapHints = [];
        if (is_array($robots) && isset($robots['body_preview'])) {
            if (preg_match_all('/^Sitemap:\s*(.+)$/im', (string) $robots['body_preview'], $m)) {
                foreach ($m[1] as $line) {
                    $sitemapHints[] = trim($line);
                }
            }
        }

        return [
            'base_url' => rtrim($origin, '/'),
            'robots_txt_url' => $robotsUrl,
            'robots' => $robots,
            'robots_error' => $robotsError,
            'sitemap_directives' => array_slice($sitemapHints, 0, 20),
        ];
    }
}
