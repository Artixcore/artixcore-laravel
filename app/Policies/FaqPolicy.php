<?php

namespace App\Policies;

use App\Models\Faq;
use App\Models\User;

class FaqPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('faqs.view_any') || $user->can('faqs.manage');
    }

    public function view(User $user, Faq $faq): bool
    {
        return $user->can('faqs.view') || $user->can('faqs.manage');
    }

    public function create(User $user): bool
    {
        return $user->can('faqs.create') || $user->can('faqs.manage');
    }

    public function update(User $user, Faq $faq): bool
    {
        return $user->can('faqs.update') || $user->can('faqs.manage');
    }

    public function delete(User $user, Faq $faq): bool
    {
        return $user->can('faqs.delete') || $user->can('faqs.manage');
    }
}
