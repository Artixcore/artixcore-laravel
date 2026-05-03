<?php

namespace App\Services\Admin;

use App\Models\CrmContact;
use App\Models\CrmContactNote;
use App\Models\CrmProject;
use App\Models\CrmSource;
use App\Models\Testimonial;
use Illuminate\Support\Facades\Schema;
use Throwable;

class CrmDashboardStatsService
{
    /**
     * @return array<string, mixed>
     */
    public function stats(): array
    {
        $defaults = [
            'total_contacts' => 0,
            'new_contacts' => 0,
            'qualified_contacts' => 0,
            'clients' => 0,
            'running_projects' => 0,
            'contacts_by_source' => [],
            'contacts_by_source_labels' => [],
            'contacts_by_service_interest' => [],
            'contacts_by_industry' => [],
            'recent_contacts' => collect(),
            'recent_notes' => collect(),
            'recent_email_activity' => collect(),
            'pending_reviews' => 0,
        ];

        try {
            if (! Schema::hasTable('crm_contacts')) {
                return $defaults;
            }

            $defaults['total_contacts'] = (int) CrmContact::query()->count();
            $defaults['new_contacts'] = (int) CrmContact::query()->where('status', CrmContact::STATUS_NEW)->count();
            $defaults['qualified_contacts'] = (int) CrmContact::query()->where('status', CrmContact::STATUS_QUALIFIED)->count();
            $defaults['clients'] = (int) CrmContact::query()->where(function ($q): void {
                $q->whereIn('type', [CrmContact::TYPE_CLIENT, CrmContact::TYPE_PARTNER])
                    ->orWhereIn('status', [CrmContact::STATUS_ACTIVE_CLIENT, CrmContact::STATUS_WON]);
            })->count();

            if (Schema::hasTable('crm_projects')) {
                $defaults['running_projects'] = (int) CrmProject::query()
                    ->whereIn('status', [CrmProject::STATUS_ACTIVE, CrmProject::STATUS_PROPOSAL])
                    ->count();
            }

            if (Schema::hasTable('crm_sources')) {
                $defaults['contacts_by_source'] = CrmContact::query()
                    ->selectRaw('source_id, count(*) as c')
                    ->whereNotNull('source_id')
                    ->groupBy('source_id')
                    ->pluck('c', 'source_id')
                    ->all();
                $sourceNames = CrmSource::query()->pluck('name', 'id')->all();
                $defaults['contacts_by_source_labels'] = $sourceNames;
            }

            $defaults['contacts_by_service_interest'] = CrmContact::query()
                ->selectRaw('COALESCE(NULLIF(service_interest, ""), "(none)") as svc, count(*) as c')
                ->groupBy('svc')
                ->orderByDesc('c')
                ->limit(12)
                ->pluck('c', 'svc')
                ->all();

            $defaults['contacts_by_industry'] = CrmContact::query()
                ->selectRaw('COALESCE(NULLIF(industry, ""), "(none)") as ind, count(*) as c')
                ->groupBy('ind')
                ->orderByDesc('c')
                ->limit(12)
                ->pluck('c', 'ind')
                ->all();

            $defaults['recent_contacts'] = CrmContact::query()
                ->with('source')
                ->orderByDesc('created_at')
                ->limit(8)
                ->get();

            if (Schema::hasTable('crm_contact_notes')) {
                $defaults['recent_notes'] = CrmContactNote::query()
                    ->with(['contact', 'user'])
                    ->orderByDesc('created_at')
                    ->limit(10)
                    ->get();

                $defaults['recent_email_activity'] = CrmContactNote::query()
                    ->where('type', CrmContactNote::TYPE_EMAIL)
                    ->with(['contact', 'user'])
                    ->orderByDesc('created_at')
                    ->limit(8)
                    ->get();
            }

            if (Schema::hasTable('testimonials') && Schema::hasColumn('testimonials', 'moderation_status')) {
                $defaults['pending_reviews'] = (int) Testimonial::query()->where('moderation_status', 'pending')->count();
            }
        } catch (Throwable) {
            return $defaults;
        }

        return $defaults;
    }
}
