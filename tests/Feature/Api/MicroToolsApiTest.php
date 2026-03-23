<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MicroToolsApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_tools_catalog_and_dns_run(): void
    {
        $this->seed();

        $catalog = $this->getJson('/api/v1/tools')
            ->assertOk()
            ->assertJsonStructure(['data' => [['slug', 'title', 'category', 'category_slug', 'execution_mode', 'access_type', 'locked', 'ads_expected']]])
            ->json('data');

        $dnsRow = collect($catalog)->firstWhere('slug', 'dns-lookup');
        $this->assertNotNull($dnsRow);
        $this->assertSame('domain-dns', $dnsRow['category_slug']);

        $this->getJson('/api/v1/tools/dns-lookup')
            ->assertOk()
            ->assertJsonPath('data.slug', 'dns-lookup');

        $this->postJson('/api/v1/tools/dns-lookup/run', [
            'hostname' => 'example.com',
        ])
            ->assertOk()
            ->assertJsonStructure(['data' => ['hostname', 'records'], 'meta']);
    }
}
