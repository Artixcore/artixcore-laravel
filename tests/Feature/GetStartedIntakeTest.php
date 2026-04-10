<?php

namespace Tests\Feature;

use App\Models\AiAgent;
use App\Models\AiMessage;
use App\Models\AiProvider;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GetStartedIntakeTest extends TestCase
{
    use RefreshDatabase;

    private const TOKEN = '0123456789abcdef01234567';

    private function seedOpenAiAgent(string $slug = 'intake-agent'): void
    {
        AiProvider::query()->create([
            'name' => 'Test',
            'driver' => AiProvider::DRIVER_OPENAI,
            'is_enabled' => true,
            'api_key_encrypted' => 'sk-test',
            'api_key_hint' => 'test',
            'default_model' => 'gpt-4o-mini',
            'timeout_seconds' => 30,
            'priority' => 10,
        ]);

        AiAgent::query()->create([
            'name' => 'Intake helper',
            'slug' => $slug,
            'instructions' => 'Be brief and warm.',
            'status' => 'active',
            'focus' => 'general',
        ]);
    }

    public function test_get_started_page_renders(): void
    {
        $response = $this->get(route('get-started'));

        $response->assertOk()
            ->assertSee('Tell us about your needs', false);
    }

    public function test_intake_validation_requires_fields(): void
    {
        $this->withoutMiddleware(ValidateCsrfToken::class);

        $response = $this->postJson(route('get-started.store'), []);

        $response->assertStatus(422);
    }

    public function test_intake_creates_lead_conversation_and_opening_message(): void
    {
        $this->withoutMiddleware(ValidateCsrfToken::class);
        $this->seedOpenAiAgent();
        config(['ai.widget_agent_slug' => 'intake-agent']);

        Http::fake([
            'api.openai.com/*' => Http::response([
                'choices' => [
                    ['message' => ['content' => 'Hi Jane — thanks for reaching out.']],
                ],
                'model' => 'gpt-4o-mini',
                'usage' => [
                    'prompt_tokens' => 10,
                    'completion_tokens' => 8,
                ],
            ], 200),
        ]);

        $response = $this->postJson(route('get-started.store'), [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => null,
            'visitor_token' => self::TOKEN,
            'client_context' => [
                'timezone' => 'America/New_York',
                'locale' => 'en-US',
            ],
        ]);

        $response->assertOk()
            ->assertJsonPath('data.opening_message', 'Hi Jane — thanks for reaching out.')
            ->assertJsonStructure(['data' => ['conversation_public_id', 'agent_slug', 'opening_message']]);

        $this->assertDatabaseHas('leads', [
            'email' => 'jane@example.com',
            'source' => 'get_started',
            'name' => 'Jane Doe',
        ]);

        $this->assertDatabaseHas('ai_messages', [
            'role' => 'assistant',
            'content' => 'Hi Jane — thanks for reaching out.',
        ]);

        $this->assertSame(2, AiMessage::query()->count());
    }

    public function test_follow_up_chat_continues_intake_conversation(): void
    {
        $this->withoutMiddleware(ValidateCsrfToken::class);
        $this->seedOpenAiAgent();
        config(['ai.widget_agent_slug' => 'intake-agent']);

        Http::fake([
            'api.openai.com/*' => Http::sequence()
                ->push([
                    'choices' => [
                        ['message' => ['content' => 'Opening from assistant.']],
                    ],
                    'model' => 'gpt-4o-mini',
                    'usage' => ['prompt_tokens' => 5, 'completion_tokens' => 5],
                ], 200)
                ->push([
                    'choices' => [
                        ['message' => ['content' => 'Second reply.']],
                    ],
                    'model' => 'gpt-4o-mini',
                    'usage' => ['prompt_tokens' => 5, 'completion_tokens' => 5],
                ], 200),
        ]);

        $start = $this->postJson(route('get-started.store'), [
            'name' => 'Alex',
            'email' => 'alex@example.com',
            'visitor_token' => self::TOKEN,
        ]);

        $start->assertOk();
        $publicId = $start->json('data.conversation_public_id');

        $chat = $this->postJson('/api/v1/ai/chat', [
            'agent_slug' => 'intake-agent',
            'message' => 'We need a SaaS MVP in about 8 weeks.',
            'conversation_public_id' => $publicId,
            'visitor_token' => self::TOKEN,
        ]);

        $chat->assertOk()
            ->assertJsonPath('data.reply', 'Second reply.');

        $this->assertGreaterThanOrEqual(4, AiMessage::query()->count());
    }
}
