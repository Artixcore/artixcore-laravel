<?php

namespace App\Jobs;

use App\Models\AiRun;
use App\Models\AiRunLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessAiRunJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $aiRunId) {}

    public function handle(): void
    {
        $run = AiRun::query()->find($this->aiRunId);
        if ($run === null) {
            return;
        }

        $run->forceFill([
            'status' => 'running',
            'started_at' => now(),
        ])->save();

        AiRunLog::query()->create([
            'ai_run_id' => $run->id,
            'level' => 'info',
            'message' => 'Run started (queue stub).',
        ]);

        $run->forceFill([
            'status' => 'succeeded',
            'finished_at' => now(),
            'output' => [
                'ok' => true,
                'note' => 'Replace ProcessAiRunJob with real orchestration.',
            ],
        ])->save();

        AiRunLog::query()->create([
            'ai_run_id' => $run->id,
            'level' => 'info',
            'message' => 'Run completed (stub).',
        ]);
    }
}
