<?php

namespace App\Services\Intake;

use App\Models\AiAgent;
use App\Models\AiConversation;
use App\Models\Lead;
use App\Services\Ai\AiChatService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class IntakeConversationBootstrapper
{
    public function __construct(
        private AiChatService $chat,
    ) {}

    /**
     * @return array{conversation_public_id: string, opening_message: string, agent_slug: string}
     */
    public function bootstrap(
        string $name,
        string $email,
        ?string $phone,
        string $visitorToken,
        Request $request,
        array $visitorContext,
    ): array {
        $agentSlug = $this->resolveAgentSlug();
        $agent = AiAgent::query()
            ->where('slug', $agentSlug)
            ->where('status', 'active')
            ->first();

        if ($agent === null) {
            throw (new ModelNotFoundException)->setModel(AiAgent::class, [$agentSlug]);
        }

        $fingerprint = hash('sha256', $visitorToken.'|'.($request->ip() ?? ''));
        $visitorKeyHash = hash('sha256', $fingerprint);

        return DB::transaction(function () use ($name, $email, $phone, $visitorContext, $agent, $visitorKeyHash, $agentSlug): array {
            $lead = Lead::query()->create([
                'source' => 'get_started',
                'status' => Lead::STATUS_NEW,
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'visitor_context' => $visitorContext,
            ]);

            $conversation = AiConversation::query()->create([
                'ai_agent_id' => $agent->id,
                'lead_id' => $lead->id,
                'channel' => 'web',
                'visitor_key_hash' => $visitorKeyHash,
                'status' => 'open',
                'metadata' => ['intake' => true],
                'last_message_at' => now(),
            ]);

            $lead->forceFill(['ai_conversation_id' => $conversation->id])->save();

            $userLine = 'Hi — I just shared my contact details on your form and I\'m ready to continue. I\'d like help figuring out the right fit for what I need.';

            $conversation->messages()->create([
                'role' => 'user',
                'content' => $userLine,
                'provider_driver' => null,
                'prompt_tokens' => null,
                'completion_tokens' => null,
            ]);

            $conversation->load(['lead', 'agent']);

            $opening = $this->chat->generateOpeningAssistantReply($conversation);

            return [
                'conversation_public_id' => $conversation->public_id,
                'opening_message' => $opening,
                'agent_slug' => $agentSlug,
            ];
        });
    }

    private function resolveAgentSlug(): string
    {
        $intake = config('ai.intake_agent_slug');
        if (is_string($intake) && $intake !== '') {
            return $intake;
        }

        $widget = config('ai.widget_agent_slug');
        if (is_string($widget) && $widget !== '') {
            return $widget;
        }

        throw new InvalidArgumentException('No intake AI agent is configured. Set AI_INTAKE_AGENT_SLUG or AI_WIDGET_AGENT_SLUG.');
    }
}
