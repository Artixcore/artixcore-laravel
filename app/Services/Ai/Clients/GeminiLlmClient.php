<?php

namespace App\Services\Ai\Clients;

use App\Models\AiProvider;
use App\Services\Ai\Contracts\LlmClientInterface;
use App\Services\Ai\Exceptions\LlmTransportException;
use App\Services\Ai\LlmCompletionResult;
use Illuminate\Support\Facades\Http;

class GeminiLlmClient implements LlmClientInterface
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

        $base = rtrim(
            is_string($provider->base_url) && $provider->base_url !== ''
                ? $provider->base_url
                : config('ai.default_gemini_base', 'https://generativelanguage.googleapis.com/v1beta/'),
            '/'
        );

        $modelId = str_starts_with($model, 'models/') ? $model : 'models/'.$model;
        $url = $base.'/'.$modelId.':generateContent';

        $timeout = max(5, (int) $provider->timeout_seconds);

        $systemParts = [];
        $contents = [];

        foreach ($messages as $msg) {
            $role = $msg['role'];
            $text = $msg['content'];
            if ($role === 'system') {
                $systemParts[] = ['text' => $text];

                continue;
            }
            $geminiRole = $role === 'assistant' ? 'model' : 'user';
            $contents[] = [
                'role' => $geminiRole,
                'parts' => [['text' => $text]],
            ];
        }

        $body = ['contents' => $contents];
        if ($systemParts !== []) {
            $body['systemInstruction'] = ['parts' => $systemParts];
        }

        if ($maxOutputTokens !== null && $maxOutputTokens > 0) {
            $body['generationConfig'] = ['maxOutputTokens' => $maxOutputTokens];
        }

        $response = Http::timeout($timeout)
            ->retry(2, 200, throw: false)
            ->acceptJson()
            ->post($url.'?key='.urlencode($key), $body);

        if (! $response->successful()) {
            throw new LlmTransportException(
                'Gemini API error: HTTP '.$response->status()
            );
        }

        $data = $response->json();
        $parts = data_get($data, 'candidates.0.content.parts');
        if (! is_array($parts) || $parts === []) {
            throw new LlmTransportException('Gemini returned an empty completion.');
        }

        $text = '';
        foreach ($parts as $part) {
            if (isset($part['text']) && is_string($part['text'])) {
                $text .= $part['text'];
            }
        }

        if ($text === '') {
            throw new LlmTransportException('Gemini returned an empty completion.');
        }

        $usage = $data['usageMetadata'] ?? [];

        return new LlmCompletionResult(
            content: $text,
            providerDriver: $provider->driver,
            model: $model,
            promptTokens: isset($usage['promptTokenCount']) ? (int) $usage['promptTokenCount'] : null,
            completionTokens: isset($usage['candidatesTokenCount']) ? (int) $usage['candidatesTokenCount'] : null,
        );
    }
}
