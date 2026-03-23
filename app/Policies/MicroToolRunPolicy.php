<?php

namespace App\Policies;

use App\Models\MicroToolRun;
use App\Models\User;

class MicroToolRunPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('micro_tool_runs.view_any');
    }

    public function view(User $user, MicroToolRun $microToolRun): bool
    {
        return $user->can('micro_tool_runs.view');
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, MicroToolRun $microToolRun): bool
    {
        return false;
    }

    public function delete(User $user, MicroToolRun $microToolRun): bool
    {
        return false;
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }
}
