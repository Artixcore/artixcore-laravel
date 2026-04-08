<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SiteSetting extends Model
{
    protected $fillable = [
        'logo_media_id',
        'favicon_media_id',
        'og_default_media_id',
        'site_name',
        'default_meta_title',
        'default_meta_description',
        'contact_email',
        'social_links',
        'design_tokens',
        'theme_default',
        'homepage_content',
        'about_content',
    ];

    protected function casts(): array
    {
        return [
            'social_links' => 'array',
            'design_tokens' => 'array',
            'homepage_content' => 'array',
            'about_content' => 'array',
        ];
    }

    public static function instance(): self
    {
        static::query()->firstOrCreate(
            ['id' => 1],
            ['theme_default' => 'system']
        );

        return static::query()->whereKey(1)->firstOrFail();
    }

    /**
     * @return BelongsTo<MediaAsset, $this>
     */
    public function logoMedia(): BelongsTo
    {
        return $this->belongsTo(MediaAsset::class, 'logo_media_id');
    }

    /**
     * @return BelongsTo<MediaAsset, $this>
     */
    public function faviconMedia(): BelongsTo
    {
        return $this->belongsTo(MediaAsset::class, 'favicon_media_id');
    }

    /**
     * @return BelongsTo<MediaAsset, $this>
     */
    public function ogDefaultMedia(): BelongsTo
    {
        return $this->belongsTo(MediaAsset::class, 'og_default_media_id');
    }
}
