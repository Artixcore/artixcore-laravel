<?php

namespace App\Services\Ai\Contracts;

use App\Models\AiProvider;
use App\Services\Ai\LlmCompletionResult;

interface LlmClientInterface
{
    /**
     * @param  list<array{role: string, content: string}>  $messages
     */
    public function complete(
        AiProvider $provider,
        array $messages,
        string $model,
        ?int $maxOutputTokens,
    ): LlmCompletionResult;
}
