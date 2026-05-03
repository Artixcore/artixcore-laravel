<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\StoreWebLeadRequest;
use App\Models\Lead;
use App\Notifications\LeadSubmitted;
use App\Services\Captcha\CaptchaVerifier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;
use Throwable;

class LeadController extends Controller
{
    public function __construct(
        private CaptchaVerifier $captchaVerifier,
    ) {}

    public function create(): View
    {
        session()->put('lead_form_loaded_at', time());

        return view('pages.lead', [
            'captchaDriver' => config('captcha.driver', 'turnstile'),
            'turnstileSiteKey' => (string) (config('services.turnstile.site_key') ?: config('captcha.turnstile.site_key', '')),
            'recaptchaSiteKey' => config('captcha.recaptcha_v2.site_key', ''),
            'captchaBypass' => $this->captchaVerifier->allowsBypass(),
        ]);
    }

    public function store(StoreWebLeadRequest $request): JsonResponse|RedirectResponse
    {
        $data = $request->validated();

        try {
            $lead = Lead::query()->create([
                'source' => $data['source'] ?? 'website',
                'status' => Lead::STATUS_NEW,
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'service_type' => $data['service_type'],
                'message' => $data['message'],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'submitted_at' => now(),
            ]);

            $this->notifyAdmins($lead);
        } catch (Throwable $e) {
            report($e);

            Log::error('Lead submission failed.', [
                'route' => $request->route()?->getName(),
                'request_id' => $request->header('X-Request-Id') ?? $request->header('X-Correlation-Id'),
                'ip' => $request->ip(),
                'exception' => $e::class,
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'ok' => false,
                    'message' => __('Something went wrong. Please try again shortly.'),
                ], 500);
            }

            return redirect()
                ->route('lead')
                ->withInput($request->except(['captcha', 'cf-turnstile-response', 'g-recaptcha-response']))
                ->withErrors(['message' => __('Something went wrong. Please try again shortly.')]);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'message' => __('Thank you for contacting Artixcore.'),
                'lead' => [
                    'name' => $lead->name,
                    'email' => $lead->email,
                ],
            ], 201);
        }

        return redirect()
            ->route('lead')
            ->with('status', __('Thanks — we received your project request and will get back to you soon.'));
    }

    private function notifyAdmins(Lead $lead): void
    {
        $to = config('mail.leads_notification_email');
        if (! is_string($to) || $to === '') {
            Log::info('Lead stored; no LEADS_NOTIFICATION_EMAIL configured.', ['lead_id' => $lead->id]);

            return;
        }

        $mailer = config('mail.default');
        if (! is_string($mailer) || $mailer === 'log' || $mailer === 'array') {
            Log::info('Lead stored; mail not configured for notifications.', ['lead_id' => $lead->id]);

            return;
        }

        try {
            Notification::route('mail', $to)->notify(new LeadSubmitted($lead));
        } catch (Throwable $e) {
            report($e);
            Log::warning('Lead notification mail failed.', ['lead_id' => $lead->id]);
        }
    }
}
