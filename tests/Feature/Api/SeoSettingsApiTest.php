<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Services\SeoSettingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SeoSettingsApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_get_returns_json_data(): void
    {
        $this->seed();

        $this->getJson('/api/v1/seo-settings')
            ->assertOk()
            ->assertJsonStructure(['data']);
    }

    public function test_public_get_omits_disabled_platforms(): void
    {
        $this->seed();

        $this->getJson('/api/v1/seo-settings')
            ->assertOk()
            ->assertJsonPath('data', []);
    }

    public function test_put_requires_authentication(): void
    {
        $this->seed();

        $payload = app(SeoSettingsService::class)->getForAdmin()['seo'];

        $this->putJson('/api/v1/seo-settings', ['seo' => $payload])
            ->assertUnauthorized();
    }

    public function test_put_updates_and_public_payload_reflects_changes(): void
    {
        $this->seed();

        /** @var User $user */
        $user = User::query()->where('email', 'master@artixcore.com')->firstOrFail();
        $token = $user->createToken('test')->plainTextToken;

        $payload = app(SeoSettingsService::class)->getForAdmin()['seo'];
        $payload['meta']['enabled'] = true;
        $payload['meta']['pixel_id'] = '1234567890123456';
        $payload['meta']['pixel_id_active'] = true;

        $this->putJson('/api/v1/seo-settings', ['seo' => $payload], [
            'Authorization' => 'Bearer '.$token,
        ])
            ->assertOk()
            ->assertJsonPath('data.success', true);

        $this->getJson('/api/v1/seo-settings')
            ->assertOk()
            ->assertJsonPath('data.meta.pixel_id', '1234567890123456');
    }

    public function test_put_invalidates_cache(): void
    {
        $this->seed();

        /** @var User $user */
        $user = User::query()->where('email', 'master@artixcore.com')->firstOrFail();
        $token = $user->createToken('test')->plainTextToken;

        $service = app(SeoSettingsService::class);
        $service->cachedSnapshot();
        $this->assertTrue(Cache::has(SeoSettingsService::CACHE_KEY));

        $payload = $service->getForAdmin()['seo'];
        $payload['google']['enabled'] = true;
        $payload['google']['ga4_measurement_id'] = 'G-TEST12345';
        $payload['google']['ga4_measurement_id_active'] = true;

        $this->putJson('/api/v1/seo-settings', ['seo' => $payload], [
            'Authorization' => 'Bearer '.$token,
        ])->assertOk();

        $this->assertFalse(Cache::has(SeoSettingsService::CACHE_KEY));

        $this->getJson('/api/v1/seo-settings')
            ->assertOk()
            ->assertJsonPath('data.google.ga4_measurement_id', 'G-TEST12345');
    }
}
