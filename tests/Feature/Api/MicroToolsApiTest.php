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

        $this->getJson('/api/v1/tools')
            ->assertOk()
            ->assertJsonStructure(['data' => [['slug', 'title', 'category', 'execution_mode']]]);

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
