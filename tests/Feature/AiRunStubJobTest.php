<?php

namespace Tests\Feature;

use App\Models\AiRun;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AiRunStubJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_ai_run_observer_dispatches_stub_job(): void
    {
        $this->seed();

        $run = AiRun::query()->create([
            'ai_workflow_id' => null,
            'ai_agent_id' => null,
            'status' => 'pending',
            'input' => ['hello' => 'world'],
        ]);

        $run->refresh();

        $this->assertSame('succeeded', $run->status);
        $this->assertNotNull($run->finished_at);
        $this->assertNotNull($run->output);
        $this->assertGreaterThanOrEqual(2, $run->logs()->count());
    }
}
