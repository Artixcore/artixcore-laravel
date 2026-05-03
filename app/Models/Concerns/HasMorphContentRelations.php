<?php

namespace App\Models\Concerns;

use App\Models\ContentRelation;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasMorphContentRelations
{
    /**
     * @return MorphMany<ContentRelation, $this>
     */
    public function contentRelationsAsSource(): MorphMany
    {
        return $this->morphMany(ContentRelation::class, 'source');
    }

    /**
     * @return MorphMany<ContentRelation, $this>
     */
    public function contentRelationsAsRelated(): MorphMany
    {
        return $this->morphMany(ContentRelation::class, 'related');
    }
}
