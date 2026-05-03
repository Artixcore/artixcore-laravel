<?php

namespace App\Policies;

use App\Models\CaseStudy;
use App\Models\User;

class CaseStudyPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('case_studies.view_any');
    }

    public function view(?User $user, CaseStudy $caseStudy): bool
    {
        if ($user !== null && $user->can('case_studies.view')) {
            return true;
        }

        if ($caseStudy->status === CaseStudy::STATUS_ARCHIVED) {
            return false;
        }

        if ($caseStudy->status !== CaseStudy::STATUS_PUBLISHED) {
            return false;
        }

        return ! $caseStudy->published_at || $caseStudy->published_at->lte(now());
    }

    public function publish(User $user, CaseStudy $caseStudy): bool
    {
        return $user->can('case_studies.publish');
    }

    public function create(User $user): bool
    {
        return $user->can('case_studies.create');
    }

    public function update(User $user, CaseStudy $caseStudy): bool
    {
        return $user->can('case_studies.update');
    }

    public function delete(User $user, CaseStudy $caseStudy): bool
    {
        return $user->can('case_studies.delete');
    }
}
