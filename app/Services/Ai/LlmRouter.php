<?php

namespace App\Services\Ai;

use App\Models\AiAgent;
use App\Models\AiProvider;
use App\Services\Ai\Exceptions\LlmTransportException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Throwable;

class LlmRouter
{
    public function __construct(
        private LlmClientFactory $factory,
    ) {}

    /**
     * @param  list<array{role: string, content: string}>  $messages
     */
    public function complete(array $messages, ?AiAgent $agent = null): LlmCompletionResult
    {
        $providers = $this->orderedProviders($agent);

        if ($providers->isEmpty()) {
            throw new LlmTransportException('No enabled AI providers are configured.');
        }

        $lastError = null;

        foreach ($providers as $provider) {
            $model = $this->resolveModel($agent, $provider);
            if ($model === null || $model === '') {
                Log::warning('ai.router_skip_no_model', [
                    'provider_id' => $provider->id,
                    'driver' => $provider->driver,
                ]);

                continue;
            }

            if (! $provider->hasApiKey()) {
                continue;
            }

            try {
                $client = $this->factory->forProvider($provider);
                $maxOut = $provider->max_output_tokens !== null ? (int) $provider->max_output_tokens : null;

                return $client->complete($provider, $messages, $model, $maxOut);
            } catch (Throwable $e) {
                $lastError = $e;
                report($e);
                Log::warning('ai.provider_failed', [
                    'provider_id' => $provider->id,
                    'driver' => $provider->driver,
                    'message' => $e->getMessage(),
                ]);
            }
        }

        throw new LlmTransportException(
            'All AI providers failed.'.($lastError ? ' Last error: '.$lastError->getMessage() : '')
        );
    }

    /**
     * @return Collection<int, AiProvider>
     */
    private function orderedProviders(?AiAgent $agent): Collection
    {
        $q = AiProvider::query()
            ->where('is_enabled', true)
            ->orderBy('priority')
            ->orderBy('id');

        $all = $q->get();

        if ($agent?->default_ai_provider_id) {
            $primary = $all->firstWhere('id', $agent->default_ai_provider_id);
            if ($primary !== null) {
                $rest = $all->reject(fn (AiProvider $p): bool => $p->id === $primary->id)->values();

                return collect([$primary])->merge($rest);
            }
        }

        return $all;
    }

    private function resolveModel(?AiAgent $agent, AiProvider $provider): ?string
    {
        if ($agent !== null && is_string($agent->model_id) && $agent->model_id !== '') {
            return $agent->model_id;
        }

        return $provider->default_model;
    }
}
