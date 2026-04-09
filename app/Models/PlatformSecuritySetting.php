<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlatformSecuritySetting extends Model
{
    protected $fillable = [
        'chat_rate_limit_per_minute',
        'chat_rate_limit_per_day',
        'internal_notes',
    ];

    public static function instance(): self
    {
        static::query()->firstOrCreate(
            ['id' => 1],
            [
                'chat_rate_limit_per_minute' => 20,
                'chat_rate_limit_per_day' => 200,
            ]
        );

        return static::query()->whereKey(1)->firstOrFail();
    }
}
