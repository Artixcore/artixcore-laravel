<?php

namespace App\Services\Tools;

use App\Models\MicroTool;
use App\Models\MicroToolRun;
use App\Models\MicroToolRunResult;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use InvalidArgumentException;
use Throwable;

class ToolRunService
{
    public function __construct(private ToolRegistry $registry) {}

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

        $adFree = $user !== null;
        $limits = $this->resolveLimits($tool, $user !== null);
        $rateKey = $this->rateLimiterKey($request, $user, $tool->slug);
        $maxAttempts = $limits['per_minute'];

        if (! RateLimiter::attempt($rateKey, $maxAttempts, fn () => true, decaySeconds: 60)) {
            throw new InvalidArgumentException('Rate limit exceeded. Try again in a minute or sign in for higher limits.');
        }

        $remaining = RateLimiter::remaining($rateKey, $maxAttempts);

        $started = (int) (microtime(true) * 1000);
        $run = new MicroToolRun([
            'micro_tool_id' => $tool->id,
            'user_id' => $user?->id,
            'guest_key' => $user === null ? $this->guestKey($request) : null,
            'input_summary' => $this->redactInputSummary($tool->slug, $input),
            'status' => 'pending',
        ]);
        $run->save();

        try {
            $handler = $this->registry->handlerForSlug($tool->slug);
            $data = $handler->handle($input, $user);
            $duration = (int) (microtime(true) * 1000) - $started;

            $run->update([
                'status' => 'completed',
                'duration_ms' => $duration,
            ]);

            MicroToolRunResult::query()->create([
                'micro_tool_run_id' => $run->id,
                'payload' => $data,
            ]);

            return [
                'data' => $data,
                'run_id' => $user !== null ? $run->id : null,
                'ad_free' => $adFree,
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
    private function resolveLimits(MicroTool $tool, bool $authenticated): array
    {
        $defaults = [
            'guest_per_minute' => (int) config('micro_tools.guest_per_minute', 30),
            'auth_per_minute' => (int) config('micro_tools.auth_per_minute', 120),
        ];
        $fromTool = is_array($tool->limits) ? $tool->limits : [];
        $guest = (int) ($fromTool['guest_per_minute'] ?? $defaults['guest_per_minute']);
        $auth = (int) ($fromTool['auth_per_minute'] ?? $defaults['auth_per_minute']);

        return ['per_minute' => $authenticated ? $auth : $guest];
    }

    private function rateLimiterKey(Request $request, ?User $user, string $toolSlug): string
    {
        if ($user !== null) {
            return 'micro-tools:user:'.$user->id.':'.$toolSlug;
        }

        return 'micro-tools:guest:'.hash('sha256', $request->ip().'|'.($request->userAgent() ?? '')).':'.$toolSlug;
    }

    private function guestKey(Request $request): string
    {
        return hash('sha256', $request->ip().'|'.config('app.key'));
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
