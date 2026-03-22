<?php

namespace App\Policies;

use App\Models\ResearchPaper;
use App\Models\User;

class ResearchPaperPolicy
{
    public function view(?User $user, ResearchPaper $paper): bool
    {
        if ($paper->status !== 'published') {
            return false;
        }

        return ! $paper->published_at || $paper->published_at->lte(now());
    }
}
