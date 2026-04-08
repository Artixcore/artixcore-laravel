<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaaSPlatformsPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_saas_platforms_page_renders(): void
    {
        $this->seed();

        $res = $this->get('/saas-platforms');

        $res->assertOk();
        $res->assertSee('Build SaaS products your customers pay for every month', false);
        $res->assertSee('SaaS Platforms', false);
    }

    public function test_saas_platforms_route_is_named(): void
    {
        $this->seed();

        $this->assertSame(url('/saas-platforms'), route('saas-platforms'));
    }
}
