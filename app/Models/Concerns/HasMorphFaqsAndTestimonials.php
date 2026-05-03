<?php

namespace App\Models\Concerns;

use App\Models\Faq;
use App\Models\Testimonial;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait HasMorphFaqsAndTestimonials
{
    /**
     * @return MorphToMany<Faq, $this>
     */
    public function faqs(): MorphToMany
    {
        return $this->morphToMany(Faq::class, 'faqable', 'faqables')
            ->withPivot('sort_order')
            ->withTimestamps()
            ->orderByPivot('sort_order');
    }

    /**
     * @return MorphToMany<Testimonial, $this>
     */
    public function testimonials(): MorphToMany
    {
        return $this->morphToMany(Testimonial::class, 'testimonialable', 'testimonialables')
            ->withPivot('sort_order')
            ->withTimestamps()
            ->orderByPivot('sort_order');
    }
}
