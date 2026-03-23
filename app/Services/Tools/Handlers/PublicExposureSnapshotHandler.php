<?php

namespace App\Services\Tools\Handlers;

use App\Models\User;
use App\Services\Tools\Contracts\ToolHandlerInterface;
use InvalidArgumentException;

class PublicExposureSnapshotHandler implements ToolHandlerInterface
{
    public function __construct(
        private EmailSecurityRecordsHandler $emailRecords,
        private DnsLookupHandler $dns
    ) {}

    /**
     * @param  array<string, mixed>  $input
     * @return array<string, mixed>
     */
    public function handle(array $input, ?User $user): array
    {
        $domain = isset($input['domain']) ? trim((string) $input['domain']) : '';
        if ($domain === '') {
            throw new InvalidArgumentException('domain is required.');
        }

        $email = $this->emailRecords->handle(['domain' => $domain], $user);
        $dnsA = $this->dns->handle(['hostname' => $domain, 'types' => ['A', 'AAAA', 'NS']], $user);

        return [
            'domain' => $domain,
            'email_security' => [
                'spf_txt' => $email['spf_txt'],
                'dmarc_txt' => $email['dmarc_txt'],
            ],
            'dns' => [
                'A' => $dnsA['records']['A'] ?? [],
                'AAAA' => $dnsA['records']['AAAA'] ?? [],
                'NS' => $dnsA['records']['NS'] ?? [],
            ],
            'disclaimer' => 'Non-destructive, public DNS visibility only.',
        ];
    }
}
