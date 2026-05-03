<?php

namespace App\Services\Security;

use App\Models\AdminAccessRule;
use App\Services\Audit\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\IpUtils;

class AdminAccessControlService
{
    public function __construct(private ActivityLogger $activityLogger) {}

    public function getClientIp(Request $request): string
    {
        return (string) $request->ip();
    }

    public function ipMatchesRule(string $ip, AdminAccessRule $rule): bool
    {
        if (! $rule->is_active) {
            return false;
        }

        if ($rule->cidr !== null && $rule->cidr !== '') {
            return IpUtils::checkIp($ip, $rule->cidr);
        }

        if ($rule->ip_address !== null && $rule->ip_address !== '') {
            return IpUtils::checkIp($ip, $rule->ip_address);
        }

        return false;
    }

    /**
     * @param  self::AREA_*  $area
     */
    public function isAllowed(Request $request, string $area): bool
    {
        if (config('artixcore.admin_ip_allowlist_bypass', false)) {
            Log::warning('admin_ip_allowlist_bypass enabled', [
                'area' => $area,
                'ip' => $this->getClientIp($request),
            ]);

            return true;
        }

        $clientIp = $this->getClientIp($request);

        $query = AdminAccessRule::query()
            ->where('is_active', true)
            ->where(function ($q) use ($area): void {
                $q->where('guard_area', $area)
                    ->orWhere('guard_area', AdminAccessRule::AREA_BOTH);
            });

        if (! $query->clone()->exists()) {
            return true;
        }

        /** @var \Illuminate\Database\Eloquent\Collection<int, AdminAccessRule> $rules */
        $rules = $query->get();

        foreach ($rules as $rule) {
            if ($this->ipMatchesRule($clientIp, $rule)) {
                $rule->forceFill(['last_matched_at' => now()])->saveQuietly();

                return true;
            }
        }

        return false;
    }

    public function logIpDenied(Request $request, string $area): void
    {
        try {
            $this->activityLogger->log('security.ip_denied', null, [
                'area' => $area,
                'ip' => $this->getClientIp($request),
            ], $request);
        } catch (\Throwable) {
            //
        }
    }

    public function hasActiveRulesFor(string $area): bool
    {
        return AdminAccessRule::query()
            ->where('is_active', true)
            ->where(function ($q) use ($area): void {
                $q->where('guard_area', $area)
                    ->orWhere('guard_area', AdminAccessRule::AREA_BOTH);
            })
            ->exists();
    }
}
