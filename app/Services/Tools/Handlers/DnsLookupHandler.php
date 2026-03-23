<?php

namespace App\Services\Tools\Handlers;

use App\Models\User;
use App\Services\Tools\Contracts\ToolHandlerInterface;
use InvalidArgumentException;

class DnsLookupHandler implements ToolHandlerInterface
{
    /**
     * @param  array<string, mixed>  $input
     * @return array<string, mixed>
     */
    public function handle(array $input, ?User $user): array
    {
        $hostname = isset($input['hostname']) ? trim((string) $input['hostname']) : '';
        if ($hostname === '') {
            throw new InvalidArgumentException('hostname is required.');
        }

        if (strlen($hostname) > 253) {
            throw new InvalidArgumentException('hostname is too long.');
        }

        if (! filter_var($hostname, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
            throw new InvalidArgumentException('Invalid hostname.');
        }

        $types = isset($input['types']) && is_array($input['types'])
            ? array_map('strval', $input['types'])
            : ['A', 'AAAA', 'MX', 'TXT', 'NS', 'CNAME'];

        $allowed = ['A', 'AAAA', 'MX', 'TXT', 'NS', 'CNAME', 'SOA', 'PTR', 'CAA'];
        $types = array_values(array_intersect($types, $allowed));
        if ($types === []) {
            $types = ['A', 'AAAA', 'MX', 'TXT', 'NS', 'CNAME'];
        }

        $records = [];
        foreach ($types as $type) {
            $const = $this->phpConstantForType($type);
            if ($const === null) {
                continue;
            }
            $fetched = @dns_get_record($hostname, $const);
            if (is_array($fetched)) {
                $records[$type] = $fetched;
            } else {
                $records[$type] = [];
            }
        }

        return [
            'hostname' => $hostname,
            'records' => $records,
        ];
    }

    private function phpConstantForType(string $type): ?int
    {
        return match (strtoupper($type)) {
            'A' => DNS_A,
            'AAAA' => DNS_AAAA,
            'MX' => DNS_MX,
            'TXT' => DNS_TXT,
            'NS' => DNS_NS,
            'CNAME' => DNS_CNAME,
            'SOA' => DNS_SOA,
            'PTR' => DNS_PTR,
            'CAA' => defined('DNS_CAA') ? DNS_CAA : null,
            default => null,
        };
    }
}
