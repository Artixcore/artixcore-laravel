<?php

namespace App\Policies;

use App\Models\Testimonial;
use App\Models\User;

class TestimonialPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('testimonials.view_any') || $user->can('reviews.manage');
    }

    public function view(User $user, Testimonial $testimonial): bool
    {
        return $user->can('testimonials.view') || $user->can('reviews.manage');
    }

    public function create(User $user): bool
    {
        return $user->can('testimonials.create') || $user->can('reviews.manage');
    }

    public function update(User $user, Testimonial $testimonial): bool
    {
        return $user->can('testimonials.update') || $user->can('reviews.manage');
    }

    public function delete(User $user, Testimonial $testimonial): bool
    {
        return $user->can('testimonials.delete') || $user->can('reviews.manage');
    }
}
