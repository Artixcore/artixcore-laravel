<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\StoreIntakeLeadRequest;
use App\Services\Intake\IntakeConversationBootstrapper;
use App\Services\Intake\VisitorContextResolver;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;
use InvalidArgumentException;
use Throwable;

class GetStartedController extends Controller
{
    public function show(): View
    {
        return view('pages.get-started', [
            'meta_title' => 'Tell us about your needs',
            'meta_description' => 'Share a few details and our assistant will guide you to the right solution.',
        ]);
    }

    public function store(
        StoreIntakeLeadRequest $request,
        VisitorContextResolver $visitorContext,
        IntakeConversationBootstrapper $bootstrapper,
    ): JsonResponse {
        $this->enforceIntakeDailyLimit($request);

        $validated = $request->validated();
        $client = $validated['client_context'] ?? null;
        $context = $visitorContext->resolve(
            $request,
            is_array($client) ? $client : null
        );

        try {
            $result = $bootstrapper->bootstrap(
                $validated['name'],
                $validated['email'],
                $validated['phone'] ?? null,
                $validated['visitor_token'],
                $request,
                $context
            );
        } catch (InvalidArgumentException) {
            return response()->json([
                'message' => 'Assistant is not configured. Please contact us directly.',
            ], 503);
        } catch (ModelNotFoundException) {
            return response()->json([
                'message' => 'Assistant is not available right now.',
            ], 503);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'message' => 'We could not start the conversation. Please try again shortly.',
            ], 503);
        }

        return response()->json([
            'data' => $result,
        ]);
    }

    private function enforceIntakeDailyLimit(Request $request): void
    {
        $key = 'intake-day:'.sha1($request->ip().'|'.config('app.key'));
        $max = max(1, (int) config('intake.per_day_per_ip', 40));

        if (RateLimiter::tooManyAttempts($key, $max)) {
            throw new HttpResponseException(response()->json([
                'message' => 'Too many submissions from this network today. Please try again tomorrow.',
            ], 429));
        }

        RateLimiter::hit($key, 86400);
    }
}
