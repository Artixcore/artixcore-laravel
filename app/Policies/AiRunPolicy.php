<?php

namespace App\Policies;

use App\Models\AiRun;
use App\Models\User;

class AiRunPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('ai_runs.view_any');
    }

    public function view(User $user, AiRun $run): bool
    {
        return $user->can('ai_runs.view');
    }

    public function create(User $user): bool
    {
        return $user->can('ai_runs.create');
    }

    public function update(User $user, AiRun $run): bool
    {
        return $user->can('ai_runs.update');
    }

    public function delete(User $user, AiRun $run): bool
    {
        return $user->can('ai_runs.delete');
    }
}
