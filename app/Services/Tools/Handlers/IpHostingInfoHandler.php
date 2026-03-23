<?php

namespace App\Services\Tools\Handlers;

use App\Models\User;
use App\Services\Tools\Contracts\ToolHandlerInterface;
use App\Services\Tools\Network\HostSafetyChecker;
use InvalidArgumentException;

class IpHostingInfoHandler implements ToolHandlerInterface
{
    public function __construct(private HostSafetyChecker $hosts) {}

    /**
     * @param  array<string, mixed>  $input
     * @return array<string, mixed>
     */
    public function handle(array $input, ?User $user): array
    {
        $query = isset($input['query']) ? trim((string) $input['query']) : '';
        if ($query === '') {
            throw new InvalidArgumentException('query is required.');
        }

        $query = preg_replace('#^https?://#i', '', $query);
        $query = preg_replace('#/.*$#', '', $query);
        $query = preg_replace('#:\d+$#', '', (string) $query);

        if (filter_var($query, FILTER_VALIDATE_IP)) {
            $this->hosts->assertPublicResolvableHost($query);

            return [
                'query' => $query,
                'kind' => 'ip',
                'reverse_hostname' => @gethostbyaddr($query) ?: null,
            ];
        }

        $this->hosts->assertPublicResolvableHost($query);
        $ips = @gethostbynamel($query) ?: [];

        return [
            'query' => $query,
            'kind' => 'hostname',
            'ipv4_addresses' => $ips,
        ];
    }
}
