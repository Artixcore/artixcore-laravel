<?php

namespace App\Policies;

use App\Models\CaseStudy;
use App\Models\User;

class CaseStudyPolicy
{
    public function view(?User $user, CaseStudy $study): bool
    {
        if ($study->status !== 'published') {
            return false;
        }

        return ! $study->published_at || $study->published_at->lte(now());
    }
}
