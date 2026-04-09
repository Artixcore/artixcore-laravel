<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class AiAgent extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'instructions',
        'model_id',
        'default_ai_provider_id',
        'role_label',
        'business_name',
        'business_description',
        'business_goals',
        'tone',
        'response_style',
        'languages',
        'forbidden_topics',
        'lead_capture_schema',
        'escalation_rules',
        'availability',
        'focus',
        'tools_allowed',
        'status',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'tools_allowed' => 'array',
            'languages' => 'array',
            'lead_capture_schema' => 'array',
            'escalation_rules' => 'array',
            'availability' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::saved(function (AiAgent $agent): void {
            Cache::forget('ai.agent.public.'.$agent->slug);
            if ($agent->wasChanged('slug') && is_string($agent->getOriginal('slug'))) {
                Cache::forget('ai.agent.public.'.$agent->getOriginal('slug'));
            }
        });
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function updatedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * @return BelongsTo<AiProvider, $this>
     */
    public function defaultProvider(): BelongsTo
    {
        return $this->belongsTo(AiProvider::class, 'default_ai_provider_id');
    }

    /**
     * @return HasMany<AiConversation, $this>
     */
    public function conversations(): HasMany
    {
        return $this->hasMany(AiConversation::class, 'ai_agent_id');
    }
}
