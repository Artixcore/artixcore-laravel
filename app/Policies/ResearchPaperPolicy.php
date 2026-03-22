<?php

namespace App\Policies;

use App\Models\ResearchPaper;
use App\Models\User;

class ResearchPaperPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('research_papers.view_any');
    }

    public function view(?User $user, ResearchPaper $researchPaper): bool
    {
        if ($user !== null && $user->can('research_papers.view')) {
            return true;
        }

        if ($researchPaper->status !== 'published') {
            return false;
        }

        return ! $researchPaper->published_at || $researchPaper->published_at->lte(now());
    }

    public function create(User $user): bool
    {
        return $user->can('research_papers.create');
    }

    public function update(User $user, ResearchPaper $researchPaper): bool
    {
        return $user->can('research_papers.update');
    }

    public function delete(User $user, ResearchPaper $researchPaper): bool
    {
        return $user->can('research_papers.delete');
    }
}
