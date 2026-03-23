<?php

namespace App\Services\Tools\Handlers;

use App\Models\User;
use App\Services\Tools\Contracts\ToolHandlerInterface;
use InvalidArgumentException;

class EmailSecurityRecordsHandler implements ToolHandlerInterface
{
    /**
     * @param  array<string, mixed>  $input
     * @return array<string, mixed>
     */
    public function handle(array $input, ?User $user): array
    {
        $domain = isset($input['domain']) ? trim(strtolower((string) $input['domain'])) : '';
        if ($domain === '') {
            throw new InvalidArgumentException('domain is required.');
        }

        $domain = preg_replace('#^https?://#', '', $domain);
        $domain = preg_replace('#/.*$#', '', (string) $domain);

        if (! filter_var($domain, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
            throw new InvalidArgumentException('Invalid domain.');
        }

        $rootTxt = @dns_get_record($domain, DNS_TXT) ?: [];
        $dmarcDomain = '_dmarc.'.$domain;
        $dmarcTxt = @dns_get_record($dmarcDomain, DNS_TXT) ?: [];

        $spf = [];
        $otherTxt = [];
        foreach ($rootTxt as $row) {
            $txt = $row['txt'] ?? '';
            if (str_starts_with(strtolower($txt), 'v=spf1')) {
                $spf[] = $txt;
            } else {
                $otherTxt[] = $txt;
            }
        }

        $dmarcRecords = array_map(fn ($r) => $r['txt'] ?? '', $dmarcTxt);

        return [
            'domain' => $domain,
            'spf_txt' => $spf,
            'dmarc_host' => $dmarcDomain,
            'dmarc_txt' => $dmarcRecords,
            'other_txt_at_apex' => $otherTxt,
            'note' => 'DKIM selectors vary by provider; this check does not query arbitrary selectors.',
        ];
    }
}
