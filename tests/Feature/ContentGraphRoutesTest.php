<?php

namespace Tests\Feature;

use Database\Seeders\ArtixcoreContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContentGraphRoutesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ArtixcoreContentSeeder::class);
    }

    public function test_public_content_routes_return_ok(): void
    {
        $this->get(route('services.index'))->assertOk();
        $serviceShow = $this->get(route('services.show', 'app-development'));
        $serviceShow->assertOk();
        $serviceShow->assertSee('"@type":"FAQPage"', false);
        $this->get(route('saas-platforms'))->assertOk();
        $this->get(route('saas-platforms.show', 'dealzyro'))->assertOk();
        $this->get(route('saas-platforms.show', 'prosperofy'))->assertOk();
        $this->get(route('articles.index'))->assertOk();
        $this->get(route('portfolio.index'))->assertOk();
        $this->get(route('case-studies.index'))->assertOk();
    }
}
