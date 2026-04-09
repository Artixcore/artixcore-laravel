<?php

namespace Tests\Unit;

use App\Models\SeoSetting;
use App\Models\SiteSetting;
use App\Services\SeoSettingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoSettingsServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_resolved_head_meta_uses_site_setting_when_seo_overrides_empty(): void
    {
        $this->seed();

        $site = SiteSetting::instance();
        $site->default_meta_title = 'Site Title From Settings';
        $site->default_meta_description = 'Desc from settings';
        $site->save();

        $service = app(SeoSettingsService::class);
        $service->forgetCache();

        $head = $service->resolvedHeadMeta($site->fresh());

        $this->assertSame('Site Title From Settings', $head['og_title']);
        $this->assertSame('Desc from settings', $head['og_description']);
    }

    public function test_resolved_head_meta_prefers_meta_og_overrides_when_enabled(): void
    {
        $this->seed();

        SeoSetting::query()->where(['platform' => 'meta', 'key' => 'enabled'])->update(['value' => '1']);
        SeoSetting::query()->where(['platform' => 'meta', 'key' => 'og_title_override'])->update(['value' => 'Override Title']);
        SeoSetting::query()->where(['platform' => 'meta', 'key' => 'og_description_override'])->update(['value' => 'Override Desc']);

        $site = SiteSetting::instance();
        $site->default_meta_title = 'Site Title';
        $site->default_meta_description = 'Site Desc';
        $site->save();

        $service = app(SeoSettingsService::class);
        $service->forgetCache();

        $head = $service->resolvedHeadMeta($site->fresh());

        $this->assertSame('Override Title', $head['og_title']);
        $this->assertSame('Override Desc', $head['og_description']);
    }
}
