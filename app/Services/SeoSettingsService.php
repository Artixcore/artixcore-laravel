<?php

namespace App\Services;

use App\Models\SeoSetting;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class SeoSettingsService
{
    public const CACHE_KEY = 'seo.settings.v1';

    public const CACHE_TTL_SECONDS = 3600;

    /**
     * @return array<string, array<string, string>>
     */
    public static function keySchema(): array
    {
        return [
            'meta' => [
                'enabled',
                'pixel_id',
                'app_id',
                'og_title_override',
                'og_description_override',
                'og_image_url',
            ],
            'google' => [
                'enabled',
                'ga4_measurement_id',
                'gtm_container_id',
                'adsense_publisher_id',
                'search_console_verification',
            ],
            'twitter' => [
                'enabled',
                'card_type',
                'site_handle',
                'creator_handle',
            ],
            'tiktok' => [
                'enabled',
                'pixel_id',
                'event_settings',
            ],
            'additional' => [
                'enabled',
                'linkedin_partner_id',
                'pinterest_verification',
            ],
        ];
    }

    /**
     * Keys that use a separate is_active flag in forms (suffix _active maps to row is_active).
     *
     * @return array<string, list<string>>
     */
    public static function fieldActiveKeys(): array
    {
        return [
            'meta' => ['pixel_id', 'app_id'],
            'google' => ['ga4_measurement_id', 'gtm_container_id', 'adsense_publisher_id', 'search_console_verification'],
            'twitter' => ['card_type', 'site_handle', 'creator_handle'],
            'tiktok' => ['pixel_id'],
            'additional' => ['linkedin_partner_id', 'pinterest_verification'],
        ];
    }

    /**
     * @return array<string, array<string, array{value: string, is_active: bool}>>
     */
    public function snapshotFromDatabase(): array
    {
        $schema = self::keySchema();
        $out = [];
        foreach ($schema as $platform => $keys) {
            $out[$platform] = [];
            foreach ($keys as $key) {
                $out[$platform][$key] = ['value' => '', 'is_active' => true];
            }
        }

        foreach (SeoSetting::query()->orderBy('platform')->orderBy('key')->get() as $row) {
            if (! isset($out[$row->platform][$row->key])) {
                continue;
            }
            $out[$row->platform][$row->key] = [
                'value' => (string) ($row->value ?? ''),
                'is_active' => (bool) $row->is_active,
            ];
        }

        return $out;
    }

    /**
     * Cached snapshot (raw rows shaped by schema).
     *
     * @return array<string, array<string, array{value: string, is_active: bool}>>
     */
    public function cachedSnapshot(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL_SECONDS, fn () => $this->snapshotFromDatabase());
    }

    public function forgetCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Admin / API form shape (booleans for enabled and field *_active).
     *
     * @return array<string, mixed>
     */
    public function getForAdmin(): array
    {
        $snap = $this->snapshotFromDatabase();
        $fieldActive = self::fieldActiveKeys();
        $out = [];

        foreach (self::keySchema() as $platform => $keys) {
            $block = [];
            foreach ($keys as $key) {
                $cell = $snap[$platform][$key] ?? ['value' => '', 'is_active' => true];
                if ($key === 'enabled') {
                    $block['enabled'] = $cell['value'] === '1' || $cell['value'] === 'true';

                    continue;
                }
                $block[$key] = $cell['value'];
                $activeKey = $key.'_active';
                if (in_array($key, $fieldActive[$platform] ?? [], true)) {
                    $block[$activeKey] = $cell['is_active'];
                }
            }
            $out[$platform] = $block;
        }

        return ['seo' => $out];
    }

    /**
     * @param  array<string, array<string, mixed>>  $seo  Validated nested seo payload
     */
    public function syncFromValidated(array $seo): void
    {
        $now = now();
        $rows = [];
        $fieldActive = self::fieldActiveKeys();

        foreach (self::keySchema() as $platform => $keys) {
            $block = $seo[$platform] ?? [];
            foreach ($keys as $key) {
                if ($key === 'enabled') {
                    $enabled = filter_var($block['enabled'] ?? false, FILTER_VALIDATE_BOOLEAN);
                    $rows[] = [
                        'platform' => $platform,
                        'key' => 'enabled',
                        'value' => $enabled ? '1' : '0',
                        'is_active' => true,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];

                    continue;
                }

                $raw = $block[$key] ?? '';
                $value = is_string($raw) ? $this->sanitizeValue($platform, $key, $raw) : '';
                $isActive = true;
                if (in_array($key, $fieldActive[$platform] ?? [], true)) {
                    $isActive = filter_var($block[$key.'_active'] ?? true, FILTER_VALIDATE_BOOLEAN);
                }

                $rows[] = [
                    'platform' => $platform,
                    'key' => $key,
                    'value' => $value,
                    'is_active' => $isActive,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        SeoSetting::query()->upsert(
            $rows,
            ['platform', 'key'],
            ['value', 'is_active', 'updated_at']
        );

        $this->forgetCache();
    }

    private function sanitizeValue(string $platform, string $key, string $value): string
    {
        $value = trim($key === 'og_image_url' || str_ends_with($key, '_url') ? $value : strip_tags($value));

        if ($platform === 'twitter' && $key === 'card_type') {
            $v = strtolower($value);

            return in_array($v, ['summary', 'summary_large_image'], true) ? $v : 'summary_large_image';
        }

        if ($key === 'event_settings') {
            if ($value === '') {
                return '';
            }
            $decoded = json_decode($value, true);

            return is_array($decoded) ? json_encode($decoded) : '';
        }

        if (str_contains($key, 'description') || str_contains($key, 'override')) {
            return Str::limit($value, 2000, '');
        }

        return Str::limit($value, 500, '');
    }

    public function platformEnabled(array $snap, string $platform): bool
    {
        $v = $snap[$platform]['enabled']['value'] ?? '0';

        return $v === '1' || $v === 'true';
    }

    /**
     * Resolved head/meta for Blade (OG + Twitter + verification). Uses SEO overrides then SiteSetting.
     *
     * @return array<string, mixed>
     */
    public function resolvedHeadMeta(SiteSetting $site): array
    {
        $snap = $this->cachedSnapshot();

        $ogTitle = $site->default_meta_title ?? $site->site_name ?? config('app.name');
        $ogDescription = trim((string) ($site->default_meta_description ?? ''));
        $ogImage = $site->relationLoaded('ogDefaultMedia')
            ? $site->ogDefaultMedia?->absoluteUrl()
            : $site->ogDefaultMedia()->first()?->absoluteUrl();

        if ($this->platformEnabled($snap, 'meta')) {
            $t = trim((string) ($snap['meta']['og_title_override']['value'] ?? ''));
            if ($t !== '') {
                $ogTitle = $t;
            }
            $d = trim((string) ($snap['meta']['og_description_override']['value'] ?? ''));
            if ($d !== '') {
                $ogDescription = $d;
            }
            $img = trim((string) ($snap['meta']['og_image_url']['value'] ?? ''));
            if ($img !== '' && filter_var($img, FILTER_VALIDATE_URL)) {
                $ogImage = $img;
            }
        }

        $twitter = [
            'card' => 'summary_large_image',
            'site' => null,
            'creator' => null,
        ];
        if ($this->platformEnabled($snap, 'twitter')) {
            $card = 'summary_large_image';
            if ($snap['twitter']['card_type']['is_active'] ?? true) {
                $c = $snap['twitter']['card_type']['value'] ?? 'summary_large_image';
                $card = in_array($c, ['summary', 'summary_large_image'], true) ? $c : 'summary_large_image';
            }
            $twitter['card'] = $card;
            if (($snap['twitter']['site_handle']['is_active'] ?? true) && trim((string) ($snap['twitter']['site_handle']['value'] ?? '')) !== '') {
                $twitter['site'] = ltrim(trim($snap['twitter']['site_handle']['value']), '@');
            }
            if (($snap['twitter']['creator_handle']['is_active'] ?? true) && trim((string) ($snap['twitter']['creator_handle']['value'] ?? '')) !== '') {
                $twitter['creator'] = ltrim(trim($snap['twitter']['creator_handle']['value']), '@');
            }
        }

        $googleVerification = '';
        if ($this->platformEnabled($snap, 'google')
            && ($snap['google']['search_console_verification']['is_active'] ?? true)) {
            $googleVerification = trim((string) ($snap['google']['search_console_verification']['value'] ?? ''));
        }

        $pinterestVerification = '';
        if ($this->platformEnabled($snap, 'additional')
            && ($snap['additional']['pinterest_verification']['is_active'] ?? true)) {
            $pinterestVerification = trim((string) ($snap['additional']['pinterest_verification']['value'] ?? ''));
        }

        $fbAppId = '';
        if ($this->platformEnabled($snap, 'meta')
            && ($snap['meta']['app_id']['is_active'] ?? true)) {
            $fbAppId = trim((string) ($snap['meta']['app_id']['value'] ?? ''));
        }

        return [
            'og_title' => $ogTitle,
            'og_description' => $ogDescription,
            'og_image' => $ogImage,
            'og_type' => 'website',
            'twitter' => $twitter,
            'google_site_verification' => $googleVerification,
            'pinterest_verification' => $pinterestVerification,
            'fb_app_id' => $fbAppId,
        ];
    }

    /**
     * Flags and IDs for script partials (cached snapshot).
     *
     * @return array<string, mixed>
     */
    public function resolvedScripts(): array
    {
        $snap = $this->cachedSnapshot();

        $googleOn = $this->platformEnabled($snap, 'google');
        $metaOn = $this->platformEnabled($snap, 'meta');
        $tiktokOn = $this->platformEnabled($snap, 'tiktok');
        $additionalOn = $this->platformEnabled($snap, 'additional');

        return [
            'meta_pixel_id' => $metaOn && ($snap['meta']['pixel_id']['is_active'] ?? true)
                ? trim((string) ($snap['meta']['pixel_id']['value'] ?? ''))
                : '',
            'gtm_container_id' => $googleOn && ($snap['google']['gtm_container_id']['is_active'] ?? true)
                ? trim((string) ($snap['google']['gtm_container_id']['value'] ?? ''))
                : '',
            'ga4_measurement_id' => $googleOn && ($snap['google']['ga4_measurement_id']['is_active'] ?? true)
                ? trim((string) ($snap['google']['ga4_measurement_id']['value'] ?? ''))
                : '',
            'adsense_publisher_id' => $googleOn && ($snap['google']['adsense_publisher_id']['is_active'] ?? true)
                ? trim((string) ($snap['google']['adsense_publisher_id']['value'] ?? ''))
                : '',
            'tiktok_pixel_id' => $tiktokOn && ($snap['tiktok']['pixel_id']['is_active'] ?? true)
                ? trim((string) ($snap['tiktok']['pixel_id']['value'] ?? ''))
                : '',
            'tiktok_event_settings' => $tiktokOn
                ? trim((string) ($snap['tiktok']['event_settings']['value'] ?? ''))
                : '',
            'linkedin_partner_id' => $additionalOn && ($snap['additional']['linkedin_partner_id']['is_active'] ?? true)
                ? trim((string) ($snap['additional']['linkedin_partner_id']['value'] ?? ''))
                : '',
        ];
    }

    /**
     * Public API: grouped, sanitized; disabled platforms omitted.
     *
     * @return array<string, mixed>
     */
    public function getResolvedPublicPayload(SiteSetting $site): array
    {
        $head = $this->resolvedHeadMeta($site);
        $scripts = $this->resolvedScripts();
        $snap = $this->cachedSnapshot();
        $out = [];

        if ($this->platformEnabled($snap, 'meta')) {
            $og = array_filter([
                'title' => $head['og_title'],
                'description' => $head['og_description'] !== '' ? $head['og_description'] : null,
                'image' => $head['og_image'] ?? null,
            ], fn ($v) => $v !== null && $v !== '');
            $meta = array_filter([
                'pixel_id' => $scripts['meta_pixel_id'] !== '' ? $scripts['meta_pixel_id'] : null,
                'app_id' => trim((string) ($snap['meta']['app_id']['value'] ?? '')) !== '' && ($snap['meta']['app_id']['is_active'] ?? true)
                    ? trim((string) ($snap['meta']['app_id']['value'] ?? ''))
                    : null,
            ], fn ($v) => $v !== null && $v !== '');
            if ($og !== []) {
                $meta['og'] = $og;
            }
            if ($meta !== []) {
                $out['meta'] = $meta;
            }
        }

        if ($this->platformEnabled($snap, 'google')) {
            $google = array_filter([
                'ga4_measurement_id' => $scripts['ga4_measurement_id'] !== '' ? $scripts['ga4_measurement_id'] : null,
                'gtm_container_id' => $scripts['gtm_container_id'] !== '' ? $scripts['gtm_container_id'] : null,
                'adsense_publisher_id' => $scripts['adsense_publisher_id'] !== '' ? $scripts['adsense_publisher_id'] : null,
                'search_console_verification' => $head['google_site_verification'] !== '' ? $head['google_site_verification'] : null,
            ], fn ($v) => $v !== null && $v !== '');
            if ($google !== []) {
                $out['google'] = $google;
            }
        }

        if ($this->platformEnabled($snap, 'twitter')) {
            $twitter = array_filter([
                'card' => $head['twitter']['card'],
                'site' => $head['twitter']['site'],
                'creator' => $head['twitter']['creator'],
            ], fn ($v) => $v !== null && $v !== '');
            if ($twitter !== []) {
                $out['twitter'] = $twitter;
            }
        }

        if ($this->platformEnabled($snap, 'tiktok')) {
            $tiktokEvent = null;
            if ($scripts['tiktok_event_settings'] !== '') {
                $decoded = json_decode($scripts['tiktok_event_settings'], true);
                $tiktokEvent = is_array($decoded) ? $decoded : null;
            }
            $tiktok = array_filter([
                'pixel_id' => $scripts['tiktok_pixel_id'] !== '' ? $scripts['tiktok_pixel_id'] : null,
                'event_settings' => $tiktokEvent,
            ], fn ($v) => $v !== null && $v !== []);
            if ($tiktok !== []) {
                $out['tiktok'] = $tiktok;
            }
        }

        if ($this->platformEnabled($snap, 'additional')) {
            $additional = array_filter([
                'linkedin_partner_id' => $scripts['linkedin_partner_id'] !== '' ? $scripts['linkedin_partner_id'] : null,
                'pinterest_verification' => $head['pinterest_verification'] !== '' ? $head['pinterest_verification'] : null,
            ], fn ($v) => $v !== null && $v !== '');
            if ($additional !== []) {
                $out['additional'] = $additional;
            }
        }

        return $out;
    }
}
