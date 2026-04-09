<?php

namespace App\Services\Ai;

use App\Models\AiProvider;
use App\Services\Ai\Clients\GeminiLlmClient;
use App\Services\Ai\Clients\OpenAiCompatibleClient;
use App\Services\Ai\Contracts\LlmClientInterface;
use InvalidArgumentException;

class LlmClientFactory
{
    public function __construct(
        private OpenAiCompatibleClient $openAiCompatible,
        private GeminiLlmClient $gemini,
    ) {}

    public function forProvider(AiProvider $provider): LlmClientInterface
    {
        return match ($provider->driver) {
            AiProvider::DRIVER_OPENAI,
            AiProvider::DRIVER_GROK,
            AiProvider::DRIVER_CUSTOM => $this->openAiCompatible,
            AiProvider::DRIVER_GEMINI => $this->gemini,
            default => throw new InvalidArgumentException('Unknown AI provider driver: '.$provider->driver),
        };
    }
}
