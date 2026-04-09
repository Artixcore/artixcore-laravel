<?php

namespace App\Jobs;

use App\Models\Lead;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

/**
 * Placeholder for queued LLM summarization of lead conversations.
 */
class RegenerateLeadSummaryJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $leadId) {}

    public function handle(): void
    {
        $lead = Lead::query()->find($this->leadId);
        if ($lead === null) {
            return;
        }

        // Future: load conversation messages, call LlmRouter with a summarization prompt, update $lead->conversation_summary.
    }
}
