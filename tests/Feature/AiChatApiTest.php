<?php

namespace Tests\Feature;

use App\Models\AiAgent;
use App\Models\AiProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AiChatApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_chat_returns_404_for_unknown_agent(): void
    {
        $response = $this->postJson('/api/v1/ai/chat', [
            'agent_slug' => 'missing',
            'message' => 'Hello',
            'visitor_token' => '0123456789abcdef',
        ]);

        $response->assertStatus(404);
    }

    public function test_chat_completes_with_openai_compatible_provider(): void
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
            'name' => 'Helper',
            'slug' => 'helper',
            'instructions' => 'Be brief.',
            'status' => 'active',
            'focus' => 'general',
        ]);

        Http::fake([
            'api.openai.com/*' => Http::response([
                'choices' => [
                    ['message' => ['content' => 'Hi there!']],
                ],
                'model' => 'gpt-4o-mini',
                'usage' => [
                    'prompt_tokens' => 10,
                    'completion_tokens' => 5,
                ],
            ], 200),
        ]);

        $response = $this->postJson('/api/v1/ai/chat', [
            'agent_slug' => 'helper',
            'message' => 'Hello',
            'visitor_token' => '0123456789abcdef01234567',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.reply', 'Hi there!');

        $this->assertDatabaseHas('ai_messages', [
            'role' => 'assistant',
            'content' => 'Hi there!',
        ]);
    }
}
