<?php

namespace App\Models\Concerns;

use App\Models\Term;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait HasTerms
{
    /**
     * @return MorphToMany<Term, $this>
     */
    public function terms(): MorphToMany
    {
        return $this->morphToMany(Term::class, 'termable')->withTimestamps();
    }
}
