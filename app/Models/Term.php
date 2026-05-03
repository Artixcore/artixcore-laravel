<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Term extends Model
{
    protected $fillable = ['taxonomy_id', 'parent_id', 'slug', 'name', 'sort_order', 'meta_title', 'meta_description'];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public function taxonomy(): BelongsTo
    {
        return $this->belongsTo(Taxonomy::class);
    }

    /**
     * @return BelongsTo<Term, $this>
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Term::class, 'parent_id');
    }

    /**
     * @return HasMany<Term, $this>
     */
    public function children(): HasMany
    {
        return $this->hasMany(Term::class, 'parent_id')->orderBy('sort_order')->orderBy('name');
    }
}
