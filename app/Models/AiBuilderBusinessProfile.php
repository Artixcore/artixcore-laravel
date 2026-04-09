<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiBuilderBusinessProfile extends Model
{
    protected $fillable = [
        'business_name',
        'brand_summary',
        'business_type',
        'target_audience',
        'main_services',
        'unique_selling_points',
        'tone_of_voice',
        'offer_details',
        'location',
        'contact_details',
        'preferred_cta_goal',
        'writing_style',
        'forbidden_topics',
        'brand_colors',
        'style_notes',
    ];

    protected function casts(): array
    {
        return [
            'contact_details' => 'array',
            'brand_colors' => 'array',
        ];
    }

    public static function instance(): self
    {
        static::query()->firstOrCreate(
            ['id' => 1],
            ['business_name' => null]
        );

        return static::query()->whereKey(1)->firstOrFail();
    }
}
