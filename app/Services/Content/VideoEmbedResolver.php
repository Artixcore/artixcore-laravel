<?php

namespace App\Services\Content;

/**
 * Allowlisted YouTube / Vimeo embed URLs only (no arbitrary iframes).
 */
class VideoEmbedResolver
{
    private const ALLOW_HOSTS = [
        'www.youtube.com',
        'youtube.com',
        'youtu.be',
        'www.vimeo.com',
        'vimeo.com',
        'player.vimeo.com',
    ];

    /**
     * @return array{provider: string, embed_url: string}|null
     */
    public function resolve(?string $url): ?array
    {
        if ($url === null || trim($url) === '') {
            return null;
        }

        $parts = parse_url(trim($url));
        if ($parts === false || empty($parts['host'])) {
            return null;
        }

        $host = strtolower($parts['host']);
        if (! in_array($host, self::ALLOW_HOSTS, true)) {
            return null;
        }

        if (str_contains($host, 'youtu')) {
            return $this->youtube($parts);
        }

        return $this->vimeo($parts);
    }

    /**
     * @param  array<string, mixed>  $parts
     * @return array{provider: string, embed_url: string}|null
     */
    private function youtube(array $parts): ?array
    {
        $host = strtolower((string) ($parts['host'] ?? ''));

        if ($host === 'youtu.be') {
            $id = isset($parts['path']) ? trim((string) $parts['path'], '/') : '';
            if ($id === '' || ! preg_match('/^[a-zA-Z0-9_-]{6,}$/', $id)) {
                return null;
            }

            return [
                'provider' => 'youtube',
                'embed_url' => 'https://www.youtube-nocookie.com/embed/'.rawurlencode($id),
            ];
        }

        $path = (string) ($parts['path'] ?? '');
        if ($path === '/watch' || str_starts_with($path, '/watch')) {
            parse_str((string) ($parts['query'] ?? ''), $q);
            $id = isset($q['v']) ? (string) $q['v'] : '';
            if ($id === '' || ! preg_match('/^[a-zA-Z0-9_-]{6,}$/', $id)) {
                return null;
            }

            return [
                'provider' => 'youtube',
                'embed_url' => 'https://www.youtube-nocookie.com/embed/'.rawurlencode($id),
            ];
        }

        if (preg_match('#^/embed/([a-zA-Z0-9_-]{6,})#', $path, $m)) {
            return [
                'provider' => 'youtube',
                'embed_url' => 'https://www.youtube-nocookie.com/embed/'.$m[1],
            ];
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $parts
     * @return array{provider: string, embed_url: string}|null
     */
    private function vimeo(array $parts): ?array
    {
        $path = trim((string) ($parts['path'] ?? '/'), '/');
        if ($path === '') {
            return null;
        }

        if (preg_match('#^(?:video/)?(\d{6,})$#', $path, $m)) {
            return [
                'provider' => 'vimeo',
                'embed_url' => 'https://player.vimeo.com/video/'.$m[1],
            ];
        }

        if (preg_match('#^video/(\d{6,})$#', $path, $m)) {
            return [
                'provider' => 'vimeo',
                'embed_url' => 'https://player.vimeo.com/video/'.$m[1],
            ];
        }

        return null;
    }

    public static function allowedOriginalHosts(): array
    {
        return self::ALLOW_HOSTS;
    }
}
