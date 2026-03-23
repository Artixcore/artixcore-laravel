<?php

namespace App\Services\Tools\Network;

use InvalidArgumentException;

class HostSafetyChecker
{
    /**
     * Blocked hostnames for outbound connections (case-insensitive).
     *
     * @var list<string>
     */
    private const BLOCKED_HOSTS = [
        'localhost',
        'metadata.google.internal',
        'metadata',
    ];

    public function assertPublicResolvableHost(string $host): void
    {
        $host = strtolower(trim($host));
        if ($host === '') {
            throw new InvalidArgumentException('Host is required.');
        }

        if (in_array($host, self::BLOCKED_HOSTS, true)) {
            throw new InvalidArgumentException('This host is not allowed.');
        }

        if (filter_var($host, FILTER_VALIDATE_IP)) {
            if (! $this->isPublicIp($host)) {
                throw new InvalidArgumentException('Private or reserved IP addresses are not allowed.');
            }

            return;
        }

        if (! filter_var($host, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
            throw new InvalidArgumentException('Invalid hostname.');
        }

        $ips = @gethostbynamel($host);
        if ($ips === false || $ips === []) {
            throw new InvalidArgumentException('Could not resolve host.');
        }

        foreach ($ips as $ip) {
            if (! $this->isPublicIp($ip)) {
                throw new InvalidArgumentException('Host resolves to a private or reserved address.');
            }
        }
    }

    public function assertSafeHttpUrl(string $url): string
    {
        $url = trim($url);
        $parts = parse_url($url);
        if ($parts === false || ! isset($parts['scheme'], $parts['host'])) {
            throw new InvalidArgumentException('Invalid URL.');
        }

        $scheme = strtolower((string) $parts['scheme']);
        if (! in_array($scheme, ['http', 'https'], true)) {
            throw new InvalidArgumentException('Only http and https URLs are allowed.');
        }

        $host = strtolower((string) $parts['host']);
        $this->assertPublicResolvableHost($host);

        return $url;
    }

    private function isPublicIp(string $ip): bool
    {
        if (! filter_var($ip, FILTER_VALIDATE_IP)) {
            return false;
        }

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
            return false;
        }

        return true;
    }
}
