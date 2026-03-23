<?php

namespace App\Services\Tools\Handlers;

use App\Models\User;
use App\Services\Tools\Contracts\ToolHandlerInterface;
use App\Services\Tools\Network\HostSafetyChecker;
use InvalidArgumentException;

class SslCheckerHandler implements ToolHandlerInterface
{
    public function __construct(private HostSafetyChecker $hosts) {}

    /**
     * @param  array<string, mixed>  $input
     * @return array<string, mixed>
     */
    public function handle(array $input, ?User $user): array
    {
        $host = isset($input['host']) ? trim((string) $input['host']) : '';
        if ($host === '') {
            throw new InvalidArgumentException('host is required.');
        }

        $host = preg_replace('#^https?://#i', '', $host);
        $host = preg_replace('#/.*$#', '', (string) $host);
        $host = preg_replace('#:\d+$#', '', (string) $host);

        $port = isset($input['port']) ? (int) $input['port'] : 443;
        if ($port < 1 || $port > 65535) {
            throw new InvalidArgumentException('Invalid port.');
        }

        $this->hosts->assertPublicResolvableHost($host);

        $ctx = stream_context_create([
            'ssl' => [
                'capture_peer_cert' => true,
                'verify_peer' => false,
                'verify_peer_name' => false,
                'SNI_enabled' => true,
                'peer_name' => $host,
            ],
        ]);

        $errno = 0;
        $errstr = '';
        $socket = @stream_socket_client(
            "ssl://{$host}:{$port}",
            $errno,
            $errstr,
            (int) config('micro_tools.http_timeout_seconds', 10),
            STREAM_CLIENT_CONNECT,
            $ctx
        );

        if ($socket === false) {
            throw new InvalidArgumentException($errstr !== '' ? $errstr : 'Could not connect for certificate inspection.');
        }

        $params = stream_context_get_params($socket);
        fclose($socket);

        $cert = $params['options']['ssl']['peer_certificate'] ?? null;
        if (! is_resource($cert) && ! is_object($cert)) {
            return [
                'host' => $host,
                'port' => $port,
                'certificate' => null,
                'error' => 'No peer certificate returned.',
            ];
        }

        $parsed = openssl_x509_parse(is_resource($cert) ? $cert : (string) $cert);
        if (! is_array($parsed)) {
            return ['host' => $host, 'port' => $port, 'certificate' => null, 'error' => 'Could not parse certificate.'];
        }

        $validFrom = isset($parsed['validFrom_time_t']) ? (int) $parsed['validFrom_time_t'] : null;
        $validTo = isset($parsed['validTo_time_t']) ? (int) $parsed['validTo_time_t'] : null;
        $now = time();

        return [
            'host' => $host,
            'port' => $port,
            'subject' => $parsed['subject'] ?? [],
            'issuer' => $parsed['issuer'] ?? [],
            'valid_from' => $validFrom !== null ? gmdate('c', $validFrom) : null,
            'valid_to' => $validTo !== null ? gmdate('c', $validTo) : null,
            'is_expired' => $validTo !== null ? $now > $validTo : null,
            'is_not_yet_valid' => $validFrom !== null ? $now < $validFrom : null,
            'serial_number' => $parsed['serialNumberHex'] ?? ($parsed['serialNumber'] ?? null),
            'signature_algorithm' => $parsed['signatureTypeSN'] ?? null,
        ];
    }
}
