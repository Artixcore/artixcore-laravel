<?php

namespace App\Policies;

use App\Models\ContactMessage;
use App\Models\User;

class ContactMessagePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('contact_messages.view_any');
    }

    public function view(User $user, ContactMessage $contactMessage): bool
    {
        return $user->can('contact_messages.view');
    }

    public function delete(User $user, ContactMessage $contactMessage): bool
    {
        return $user->can('contact_messages.delete');
    }
}
