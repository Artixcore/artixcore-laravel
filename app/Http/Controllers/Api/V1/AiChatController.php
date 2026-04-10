<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\ChatAiRequest;
use App\Models\AiConversation;
use App\Models\PlatformSecuritySetting;
use App\Services\Ai\AiChatService;
use App\Services\Ai\Exceptions\LlmTransportException;
use App\Services\Lead\LeadQualificationMerger;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Throwable;

class AiChatController extends Controller
{
    public function __construct(
        private AiChatService $chat,
        private LeadQualificationMerger $qualificationMerger,
    ) {}

    public function profile(Request $request, string $slug): JsonResponse
    {
        $data = $this->chat->publicAgentProfile($slug);
        if ($data === null) {
            return response()->json(['message' => 'Agent not found.'], 404);
        }

        return response()->json(['data' => $data]);
    }

    public function chat(ChatAiRequest $request): JsonResponse
    {
        $this->enforceDailyLimit($request);

        $validated = $request->validated();
        $fingerprint = hash('sha256', $validated['visitor_token'].'|'.$request->ip());

        try {
            $result = $this->chat->handleMessage(
                agentSlug: $validated['agent_slug'],
                userMessage: $validated['message'],
                conversationPublicId: $validated['conversation_public_id'] ?? null,
                visitorFingerprint: $fingerprint,
                channel: 'web',
            );
        } catch (ModelNotFoundException) {
            return response()->json(['message' => 'Agent not found.'], 404);
        } catch (LlmTransportException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 503);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'message' => 'Chat could not be completed.',
            ], 500);
        }

        $conversation = AiConversation::query()
            ->where('public_id', $result['conversation_public_id'])
            ->first();

        if ($conversation !== null && $result['lead_hint'] !== null) {
            $this->chat->persistLeadFromHints($conversation, $result['lead_hint']);
            $conversation->refresh();
        }

        $conversation?->load('lead');
        if ($conversation?->lead !== null) {
            $this->qualificationMerger->mergeFromUserMessage($conversation->lead, $validated['message']);
        }

        return response()->json([
            'data' => [
                'reply' => $result['reply'],
                'conversation_public_id' => $result['conversation_public_id'],
            ],
        ]);
    }

    private function enforceDailyLimit(Request $request): void
    {
        $token = (string) $request->input('visitor_token', '');
        $key = 'ai-chat-day:'.sha1($request->ip().'|'.$token);

        try {
            $max = (int) PlatformSecuritySetting::instance()->chat_rate_limit_per_day;
        } catch (Throwable) {
            $max = 200;
        }

        $max = max(1, $max);

        if (RateLimiter::tooManyAttempts($key, $max)) {
            throw new HttpResponseException(response()->json(['message' => 'Daily chat limit reached. Try again tomorrow.'], 429));
        }

        RateLimiter::hit($key, 86400);
    }
}
