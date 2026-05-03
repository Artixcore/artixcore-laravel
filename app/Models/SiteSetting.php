<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;

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
        'services_page_content',
        'saas_page_content',
    ];

    protected function casts(): array
    {
        return [
            'social_links' => 'array',
            'design_tokens' => 'array',
            'homepage_content' => 'array',
            'about_content' => 'array',
            'services_page_content' => 'array',
            'saas_page_content' => 'array',
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
     * Read-only safe singleton for public views: never throws when the table is missing or DB errors occur.
     */
    public static function safeInstance(): self
    {
        try {
            if (! Schema::hasTable('site_settings')) {
                return self::fallbackInstance();
            }

            return self::instance();
        } catch (\Throwable) {
            return self::fallbackInstance();
        }
    }

    /**
     * Unsaved defaults so Blade can use $site without null checks when CMS rows are unavailable.
     */
    private static function fallbackInstance(): self
    {
        $email = (string) config('app.contact_email', 'hello@artixcore.com');

        return new self([
            'site_name' => config('app.name', 'Artixcore'),
            'default_meta_title' => config('marketing.homepage.meta_title'),
            'default_meta_description' => config('marketing.homepage.meta_description'),
            'contact_email' => $email,
            'theme_default' => 'system',
        ]);
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
