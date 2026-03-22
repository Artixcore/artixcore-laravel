<?php

namespace App\Observers;

use App\Jobs\ProcessAiRunJob;
use App\Models\AiRun;

class AiRunObserver
{
    public function created(AiRun $run): void
    {
        ProcessAiRunJob::dispatch($run->id);
    }
}
