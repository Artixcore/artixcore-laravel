<?php

namespace App\Services\Tools;

use App\Models\GuestMicroToolUsage;
use App\Models\MicroTool;
use App\Models\MicroToolAccessPlan;
use App\Models\MicroToolRun;
use App\Models\MicroToolRunResult;
use App\Models\User;
use App\Models\UserMicroToolHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use InvalidArgumentException;
use Throwable;

class ToolRunService
{
    public function __construct(
        private ToolRegistry $registry,
        private ToolAccessService $access,
    ) {}

    /**
     * @param  array<string, mixed>  $input
     * @return array{data: array<string, mixed>, run_id: int|null, ad_free: bool, limits_remaining: int|null}
     */
    public function run(MicroTool $tool, array $input, Request $request, ?User $user): array
    {
        if ($tool->execution_mode === 'client') {
            throw new InvalidArgumentException('This tool runs in your browser only.');
        }

        if (! $this->registry->hasHandler($tool->slug)) {
            throw new InvalidArgumentException('This tool is not available yet.');
        }

        $resolution = $this->access->resolveForRun($tool, $user);
        $guestToken = $user === null ? $this->guestToken($request) : null;
        $sessionId = $request->hasSession() ? $request->session()->getId() : null;
        $requestIp = $request->ip();
        $requestHash = hash('sha256', ($requestIp ?? '').'|'.($request->userAgent() ?? ''));

        if (! $resolution->canExecute) {
            $this->persistRun(
                tool: $tool,
                user: $user,
                input: $input,
                guestToken: $guestToken,
                sessionId: $sessionId,
                requestIp: $requestIp,
                requestHash: $requestHash,
                status: 'blocked',
                durationMs: null,
                adsShown: $resolution->adsExpected,
            );

            throw new InvalidArgumentException($resolution->blockReason ?? 'Access denied.');
        }

        $limits = $this->resolveLimits($tool, $user);
        $rateKey = $this->rateLimiterKey($request, $user, $tool->slug);
        $maxAttempts = $limits['per_minute'];

        if (! RateLimiter::attempt($rateKey, $maxAttempts, fn () => true, decaySeconds: 60)) {
            $this->persistRun(
                tool: $tool,
                user: $user,
                input: $input,
                guestToken: $guestToken,
                sessionId: $sessionId,
                requestIp: $requestIp,
                requestHash: $requestHash,
                status: 'rate_limited',
                durationMs: null,
                adsShown: false,
            );
            if ($user === null) {
                $this->bumpGuestUsage($tool, $guestToken, $sessionId, $request, adsShown: false);
            }

            throw new InvalidArgumentException('Rate limit exceeded. Try again in a minute or sign in for higher limits.');
        }

        $remaining = RateLimiter::remaining($rateKey, $maxAttempts);

        $started = (int) (microtime(true) * 1000);
        $run = $this->persistRun(
            tool: $tool,
            user: $user,
            input: $input,
            guestToken: $guestToken,
            sessionId: $sessionId,
            requestIp: $requestIp,
            requestHash: $requestHash,
            status: 'pending',
            durationMs: null,
            adsShown: false,
        );

        try {
            $handler = $this->registry->handlerForSlug($tool->slug);
            $data = $handler->handle($input, $user);
            $duration = (int) (microtime(true) * 1000) - $started;

            $adsShown = $resolution->adsExpected;
            $run->update([
                'status' => 'success',
                'duration_ms' => $duration,
                'result_summary' => $this->shortResultSummary($data),
                'ads_shown' => $adsShown,
            ]);

            MicroToolRunResult::query()->create([
                'micro_tool_run_id' => $run->id,
                'result_type' => 'json',
                'payload' => $data,
                'is_exportable' => true,
            ]);

            if ($user === null) {
                $this->bumpGuestUsage($tool, $guestToken, $sessionId, $request, $adsShown);
            }

            if ($user !== null) {
                UserMicroToolHistory::query()->create([
                    'user_id' => $user->id,
                    'micro_tool_id' => $tool->id,
                    'micro_tool_run_id' => $run->id,
                    'title' => $tool->title,
                    'summary' => $run->result_summary,
                    'is_saved' => true,
                    'is_favorite' => false,
                ]);
            }

            return [
                'data' => $data,
                'run_id' => $user !== null ? $run->id : null,
                'ad_free' => $resolution->adFree,
                'limits_remaining' => $remaining,
            ];
        } catch (Throwable $e) {
            $duration = (int) (microtime(true) * 1000) - $started;
            $run->update([
                'status' => 'failed',
                'duration_ms' => $duration,
                'error_code' => 'tool_error',
            ]);

            throw $e;
        }
    }

    /**
     * @return array{per_minute: int}
     */
    private function resolveLimits(MicroTool $tool, ?User $user): array
    {
        $authenticated = $user !== null;
        $paid = $user?->isCurrentlyPremium() ?? false;

        $defaults = [
            'guest_per_minute' => (int) config('micro_tools.guest_per_minute', 30),
            'auth_per_minute' => (int) config('micro_tools.auth_per_minute', 120),
        ];

        $planType = ! $authenticated
            ? MicroToolAccessPlan::PLAN_GUEST
            : ($paid ? MicroToolAccessPlan::PLAN_PREMIUM : MicroToolAccessPlan::PLAN_REGISTERED);

        $plan = $tool->accessPlans()->where('plan_type', $planType)->first();

        $fromTool = is_array($tool->limits) ? $tool->limits : [];
        $guest = (int) ($fromTool['guest_per_minute'] ?? $defaults['guest_per_minute']);
        $auth = (int) ($fromTool['auth_per_minute'] ?? $defaults['auth_per_minute']);

        if ($plan !== null && $plan->usage_limit_daily !== null) {
            // Per-minute still from JSON/config; daily enforced separately if needed later
        }

        return ['per_minute' => $authenticated ? $auth : $guest];
    }

    private function rateLimiterKey(Request $request, ?User $user, string $toolSlug): string
    {
        if ($user !== null) {
            return 'micro-tools:user:'.$user->id.':'.$toolSlug;
        }

        return 'micro-tools:guest:'.hash('sha256', $request->ip().'|'.($request->userAgent() ?? '')).':'.$toolSlug;
    }

    private function guestToken(Request $request): string
    {
        return hash('sha256', $request->ip().'|'.config('app.key'));
    }

    private function bumpGuestUsage(MicroTool $tool, ?string $guestToken, ?string $sessionId, Request $request, bool $adsShown): void
    {
        $token = $guestToken ?? 'unknown';
        $usage = GuestMicroToolUsage::query()->firstOrCreate(
            [
                'micro_tool_id' => $tool->id,
                'guest_token' => $token,
                'usage_date' => now()->toDateString(),
            ],
            [
                'session_id' => $sessionId,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'total_runs' => 0,
                'ads_shown_count' => 0,
            ]
        );

        $usage->increment('total_runs');
        if ($adsShown) {
            $usage->increment('ads_shown_count');
        }
    }

    /**
     * @param  array<string, mixed>  $input
     */
    private function persistRun(
        MicroTool $tool,
        ?User $user,
        array $input,
        ?string $guestToken,
        ?string $sessionId,
        ?string $requestIp,
        string $requestHash,
        string $status,
        ?int $durationMs,
        bool $adsShown,
    ): MicroToolRun {
        $registered = $user !== null;
        $paid = $user?->isCurrentlyPremium() ?? false;

        $run = MicroToolRun::query()->create([
            'micro_tool_id' => $tool->id,
            'user_id' => $user?->id,
            'guest_key' => $user === null ? $guestToken : null,
            'guest_token' => $guestToken,
            'session_id' => $sessionId,
            'request_ip' => $requestIp,
            'request_hash' => $requestHash,
            'input_summary' => $this->redactInputSummary($tool->slug, $input),
            'status' => $status,
            'duration_ms' => $durationMs,
            'is_guest' => ! $registered,
            'is_registered' => $registered,
            'is_aid_user' => $registered && filled($user?->aid),
            'is_paid_user' => $paid,
            'ads_shown' => $adsShown,
            'is_saved' => false,
            'source' => 'web',
        ]);

        return $run;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function shortResultSummary(array $data): ?string
    {
        $encoded = json_encode($data);

        if (! is_string($encoded)) {
            return null;
        }

        if (strlen($encoded) > 500) {
            return substr($encoded, 0, 500).'…';
        }

        return $encoded;
    }

    /**
     * @param  array<string, mixed>  $input
     * @return array<string, mixed>
     */
    private function redactInputSummary(string $slug, array $input): array
    {
        $keys = match ($slug) {
            'dns-lookup' => ['hostname', 'types'],
            'ssl-checker' => ['host', 'port'],
            'uptime-check', 'meta-tag-checker', 'social-preview-check', 'website-audit-basic', 'speed-snapshot', 'website-technology', 'mobile-friendly-hints', 'schema-markup-check', 'security-headers', 'sitemap-robots-check', 'link-safety-summary', 'phishing-suspicion' => ['url'],
            'email-security-records', 'public-exposure-snapshot' => ['domain'],
            'keyword-density' => ['text'],
            'ip-hosting-info' => ['query'],
            default => array_keys($input),
        };

        $out = [];
        foreach ($keys as $k) {
            if (! array_key_exists($k, $input)) {
                continue;
            }
            $v = $input[$k];
            if (is_string($v) && strlen($v) > 500) {
                $v = substr($v, 0, 500).'…';
            }
            $out[$k] = $v;
        }

        return $out;
    }
}
