<?php

namespace App\Services\Ai;

final class LlmCompletionResult
{
    /**
     * @param  list<array{role: string, content: string}>  $messages
     */
    public function __construct(
        public string $content,
        public string $providerDriver,
        public ?string $model = null,
        public ?int $promptTokens = null,
        public ?int $completionTokens = null,
        public ?int $aiProviderId = null,
    ) {}
}
