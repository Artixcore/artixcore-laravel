<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CrmSource extends Model
{
    use SoftDeletes;

    public const SLUG_WEBSITE_LEAD_FORM = 'website-lead-form';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * @return HasMany<CrmContact, $this>
     */
    public function contacts(): HasMany
    {
        return $this->hasMany(CrmContact::class, 'source_id');
    }

    /**
     * @param  Builder<CrmSource>  $query
     * @return Builder<CrmSource>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
