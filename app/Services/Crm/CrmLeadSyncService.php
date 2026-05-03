<?php

namespace App\Services\Crm;

use App\Models\CrmContact;
use App\Models\CrmContactNote;
use App\Models\CrmSource;
use App\Models\Lead;
use App\Services\GeoIp\GeoIpLookupService;
use Illuminate\Support\Facades\Schema;

class CrmLeadSyncService
{
    public function __construct(
        private GeoIpLookupService $geoIp,
    ) {}

    public function syncFromWebLead(Lead $lead, array $geo, ?string $ip, ?string $userAgent): void
    {
        if (! Schema::hasTable('crm_contacts') || ! Schema::hasTable('crm_sources')) {
            return;
        }

        $source = CrmSource::query()->where('slug', CrmSource::SLUG_WEBSITE_LEAD_FORM)->first();
        $sourceId = $source?->id;

        $assignee = config('crm.default_assignee_id');
        $assigneeId = is_numeric($assignee) ? (int) $assignee : null;

        $base = [
            'type' => CrmContact::TYPE_LEAD,
            'status' => CrmContact::STATUS_NEW,
            'name' => (string) ($lead->name ?? ''),
            'phone' => $lead->phone,
            'company_name' => $lead->company,
            'source_id' => $sourceId,
            'service_interest' => $lead->service_type ?? $lead->service_interest,
            'priority' => CrmContact::PRIORITY_NORMAL,
            'geo_country' => $geo['country'] ?? null,
            'geo_region' => $geo['region'] ?? null,
            'geo_city' => $geo['city'] ?? null,
            'geo_postal' => $geo['postal'] ?? null,
            'geo_latitude' => $geo['latitude'] ?? null,
            'geo_longitude' => $geo['longitude'] ?? null,
            'ip_address' => $ip,
            'user_agent' => $userAgent,
            'lead_id' => $lead->id,
            'assigned_to' => $assigneeId,
        ];

        $email = $lead->email ? strtolower(trim((string) $lead->email)) : null;
        $phone = $lead->phone ? trim((string) $lead->phone) : null;

        $contact = null;
        if ($email !== null && $email !== '') {
            $contact = CrmContact::query()->updateOrCreate(
                ['email' => $email],
                array_merge($base, ['email' => $email])
            );
        } elseif ($phone !== null && $phone !== '') {
            $contact = CrmContact::query()
                ->where('phone', $phone)
                ->where(function ($q): void {
                    $q->whereNull('email')->orWhere('email', '');
                })
                ->first();

            if ($contact) {
                $contact->update(array_merge($base, ['phone' => $phone]));
            } else {
                $contact = CrmContact::query()->create(array_merge($base, [
                    'email' => null,
                    'phone' => $phone,
                ]));
            }
        } else {
            $contact = CrmContact::query()->create(array_merge($base, [
                'email' => null,
            ]));
        }

        if ($contact === null) {
            return;
        }

        $hasNoteForLead = CrmContactNote::query()
            ->where('contact_id', $contact->id)
            ->where('type', CrmContactNote::TYPE_SYSTEM)
            ->where('metadata->lead_id', $lead->id)
            ->exists();

        if (! $hasNoteForLead) {
            CrmContactNote::query()->create([
                'contact_id' => $contact->id,
                'user_id' => null,
                'type' => CrmContactNote::TYPE_SYSTEM,
                'title' => null,
                'body' => 'Lead submitted from website.',
                'metadata' => ['lead_id' => $lead->id],
            ]);
        }
    }

    /**
     * @param  array<string, mixed>  $geo
     * @return array<string, mixed>
     */
    public function mergeVisitorContext(?array $existing, array $geo, ?string $ip, ?string $userAgent): array
    {
        $ctx = is_array($existing) ? $existing : [];
        $ctx['ip'] = $ip;
        $ctx['user_agent'] = $userAgent;
        if (array_filter($geo)) {
            $ctx['geo'] = $geo;
        }

        return $ctx;
    }
}
