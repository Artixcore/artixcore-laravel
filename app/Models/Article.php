<?php

namespace App\Models;

use App\Models\Concerns\HasTerms;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasTerms;

    protected $fillable = [
        'slug',
        'title',
        'summary',
        'body',
        'meta_title',
        'meta_description',
        'status',
        'featured',
        'view_count',
        'trending_score',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'featured' => 'boolean',
            'view_count' => 'integer',
            'trending_score' => 'integer',
            'published_at' => 'datetime',
        ];
    }

    /**
     * @param  Builder<Article>  $query
     * @return Builder<Article>
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
