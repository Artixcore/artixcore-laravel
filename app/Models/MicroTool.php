<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MicroTool extends Model
{
    protected $fillable = [
        'micro_tool_category_id',
        'slug',
        'category',
        'title',
        'description',
        'short_description',
        'route_path',
        'tool_type',
        'input_type',
        'output_type',
        'access_type',
        'is_public',
        'requires_auth',
        'ads_enabled',
        'is_featured',
        'version',
        'created_by',
        'updated_by',
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
            'is_public' => 'boolean',
            'requires_auth' => 'boolean',
            'ads_enabled' => 'boolean',
            'is_featured' => 'boolean',
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

    public function toolCategory(): BelongsTo
    {
        return $this->belongsTo(MicroToolCategory::class, 'micro_tool_category_id');
    }

    public function runs(): HasMany
    {
        return $this->hasMany(MicroToolRun::class, 'micro_tool_id');
    }

    public function settings(): HasMany
    {
        return $this->hasMany(MicroToolSetting::class, 'micro_tool_id');
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(MicroToolStatusLog::class, 'micro_tool_id');
    }

    public function accessPlans(): HasMany
    {
        return $this->hasMany(MicroToolAccessPlan::class, 'micro_tool_id');
    }

    public function favoritedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'micro_tool_favorites', 'micro_tool_id', 'user_id')
            ->withTimestamps();
    }

    public function getCategorySlugAttribute(): string
    {
        return $this->toolCategory?->slug ?? $this->category ?? 'uncategorized';
    }
}
