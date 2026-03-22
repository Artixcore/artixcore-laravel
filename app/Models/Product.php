<?php

namespace App\Models;

use App\Models\Concerns\HasTerms;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasTerms;

    protected $fillable = [
        'slug',
        'title',
        'tagline',
        'summary',
        'body',
        'meta_title',
        'meta_description',
        'status',
        'featured',
        'sort_order',
        'view_count',
        'trending_score',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'featured' => 'boolean',
            'sort_order' => 'integer',
            'view_count' => 'integer',
            'trending_score' => 'integer',
            'published_at' => 'datetime',
        ];
    }

    /**
     * @param  Builder<Product>  $query
     * @return Builder<Product>
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('status', 'published')
            ->where(function (Builder $q): void {
                $q->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });
    }
}
