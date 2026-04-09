<?php

namespace App\Services\Ai\Clients;

use App\Models\AiProvider;
use App\Services\Ai\Contracts\LlmClientInterface;
use App\Services\Ai\Exceptions\LlmTransportException;
use App\Services\Ai\LlmCompletionResult;
use Illuminate\Support\Facades\Http;

class OpenAiCompatibleClient implements LlmClientInterface
{
    public function complete(
        AiProvider $provider,
        array $messages,
        string $model,
        ?int $maxOutputTokens,
    ): LlmCompletionResult {
        $key = $provider->api_key_encrypted;
        if (! is_string($key) || $key === '') {
            throw new LlmTransportException('Provider API key is not configured.');
        }

        $base = rtrim($this->resolveBaseUrl($provider), '/');
        $url = $base.'/chat/completions';

        $payload = [
            'model' => $model,
            'messages' => array_map(static fn (array $m): array => [
                'role' => $m['role'],
                'content' => $m['content'],
            ], $messages),
        ];

        if ($maxOutputTokens !== null && $maxOutputTokens > 0) {
            $payload['max_tokens'] = $maxOutputTokens;
        }

        $meta = is_array($provider->metadata) ? $provider->metadata : [];
        if (isset($meta['temperature']) && is_numeric($meta['temperature'])) {
            $payload['temperature'] = (float) $meta['temperature'];
        }
        if (isset($meta['top_p']) && is_numeric($meta['top_p'])) {
            $payload['top_p'] = (float) $meta['top_p'];
        }

        $timeout = max(5, (int) $provider->timeout_seconds);

        $response = Http::withToken($key)
            ->timeout($timeout)
            ->retry(2, 200, throw: false)
            ->acceptJson()
            ->post($url, $payload);

        if (! $response->successful()) {
            throw new LlmTransportException(
                'OpenAI-compatible API error: HTTP '.$response->status()
            );
        }

        $data = $response->json();
        $text = data_get($data, 'choices.0.message.content');
        if (! is_string($text) || $text === '') {
            throw new LlmTransportException('OpenAI-compatible API returned an empty completion.');
        }

        $usage = $data['usage'] ?? [];

        return new LlmCompletionResult(
            content: $text,
            providerDriver: $provider->driver,
            model: is_string($data['model'] ?? null) ? $data['model'] : $model,
            promptTokens: isset($usage['prompt_tokens']) ? (int) $usage['prompt_tokens'] : null,
            completionTokens: isset($usage['completion_tokens']) ? (int) $usage['completion_tokens'] : null,
            aiProviderId: $provider->id,
        );
    }

    private function resolveBaseUrl(AiProvider $provider): string
    {
        if (is_string($provider->base_url) && $provider->base_url !== '') {
            return $provider->base_url;
        }

        return match ($provider->driver) {
            AiProvider::DRIVER_GROK => config('ai.default_grok_base', 'https://api.x.ai/v1'),
            AiProvider::DRIVER_CUSTOM => config('ai.default_openai_base'),
            default => config('ai.default_openai_base', 'https://api.openai.com/v1'),
        };
    }
}
