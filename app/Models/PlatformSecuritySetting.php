<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlatformSecuritySetting extends Model
{
    protected $fillable = [
        'chat_rate_limit_per_minute',
        'chat_rate_limit_per_day',
        'builder_ai_rate_limit_per_minute',
        'internal_notes',
    ];

    public static function instance(): self
    {
        static::query()->firstOrCreate(
            ['id' => 1],
            [
                'chat_rate_limit_per_minute' => 20,
                'chat_rate_limit_per_day' => 200,
                'builder_ai_rate_limit_per_minute' => 30,
            ]
        );

        return static::query()->whereKey(1)->firstOrFail();
    }
}
