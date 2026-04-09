<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AiProvider extends Model
{
    protected $fillable = [
        'name',
        'driver',
        'is_enabled',
        'api_key_encrypted',
        'api_key_hint',
        'default_model',
        'base_url',
        'timeout_seconds',
        'priority',
        'max_output_tokens',
        'rate_limit_json',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'is_enabled' => 'boolean',
            'api_key_encrypted' => 'encrypted',
            'rate_limit_json' => 'array',
            'metadata' => 'array',
        ];
    }

    public const DRIVER_OPENAI = 'openai';

    public const DRIVER_GEMINI = 'gemini';

    public const DRIVER_GROK = 'grok';

    public const DRIVER_CUSTOM = 'custom';

    /**
     * @return HasMany<AiAgent, $this>
     */
    public function agents(): HasMany
    {
        return $this->hasMany(AiAgent::class, 'default_ai_provider_id');
    }

    public function hasApiKey(): bool
    {
        $v = $this->api_key_encrypted;

        return is_string($v) && $v !== '';
    }
}
