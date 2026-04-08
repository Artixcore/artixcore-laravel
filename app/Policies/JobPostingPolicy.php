<?php

namespace App\Policies;

use App\Models\JobPosting;
use App\Models\User;

class JobPostingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('job_postings.view_any');
    }

    public function view(User $user, JobPosting $jobPosting): bool
    {
        return $user->can('job_postings.view');
    }

    public function create(User $user): bool
    {
        return $user->can('job_postings.create');
    }

    public function update(User $user, JobPosting $jobPosting): bool
    {
        return $user->can('job_postings.update');
    }

    public function delete(User $user, JobPosting $jobPosting): bool
    {
        return $user->can('job_postings.delete');
    }
}
