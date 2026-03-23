<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MicroTool extends Model
{
    protected $fillable = [
        'slug',
        'category',
        'title',
        'description',
        'icon_key',
        'execution_mode',
        'limits',
        'input_schema',
        'is_active',
        'is_premium',
        'sort_order',
        'released_at',
        'featured_score',
        'is_popular',
        'is_new',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'limits' => 'array',
            'input_schema' => 'array',
            'is_active' => 'boolean',
            'is_premium' => 'boolean',
            'released_at' => 'datetime',
            'is_popular' => 'boolean',
            'is_new' => 'boolean',
        ];
    }

    /**
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function runs(): HasMany
    {
        return $this->hasMany(MicroToolRun::class, 'micro_tool_id');
    }

    public function favoritedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'micro_tool_favorites', 'micro_tool_id', 'user_id')
            ->withTimestamps();
    }
}
