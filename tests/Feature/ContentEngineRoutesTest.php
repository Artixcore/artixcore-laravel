<?php

namespace Tests\Feature;

use App\Models\CaseStudy;
use App\Models\MarketUpdate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContentEngineRoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_portfolio_root_redirects_to_case_studies(): void
    {
        $this->get('/portfolio')->assertRedirect('/case-studies');
    }

    public function test_portfolio_slug_redirects_to_case_study_show(): void
    {
        $this->get('/portfolio/sample-slug')->assertRedirect(route('case-studies.show', ['slug' => 'sample-slug']));
    }

    public function test_case_studies_index_loads(): void
    {
        $this->get(route('case-studies.index'))->assertOk();
    }

    public function test_market_updates_index_loads(): void
    {
        $this->get(route('market-updates.index'))->assertOk();
    }

    public function test_published_case_study_detail_loads(): void
    {
        CaseStudy::query()->create([
            'slug' => 'ship-fast',
            'title' => 'Ship fast',
            'summary' => 'Summary',
            'body' => '<p>Body</p>',
            'status' => CaseStudy::STATUS_PUBLISHED,
            'published_at' => now()->subDay(),
            'author_name' => 'Ali 1.0',
            'author_type' => CaseStudy::AUTHOR_TYPE_AI,
        ]);

        $this->get(route('case-studies.show', 'ship-fast'))->assertOk();
    }

    public function test_draft_case_study_not_public(): void
    {
        CaseStudy::query()->create([
            'slug' => 'secret-draft',
            'title' => 'Draft',
            'summary' => null,
            'body' => '<p>X</p>',
            'status' => CaseStudy::STATUS_DRAFT,
            'published_at' => null,
            'author_name' => 'Human',
            'author_type' => CaseStudy::AUTHOR_TYPE_HUMAN,
        ]);

        $this->get(route('case-studies.show', 'secret-draft'))->assertNotFound();
    }

    public function test_published_market_update_detail_loads(): void
    {
        MarketUpdate::query()->create([
            'slug' => 'weekly-scan',
            'title' => 'Weekly scan',
            'excerpt' => 'Excerpt',
            'body' => '<p>Body</p>',
            'status' => MarketUpdate::STATUS_PUBLISHED,
            'published_at' => now()->subDay(),
            'author_name' => 'Ali 1.0',
            'author_type' => MarketUpdate::AUTHOR_TYPE_AI,
        ]);

        $this->get(route('market-updates.show', 'weekly-scan'))->assertOk();
    }

    public function test_guest_cannot_view_draft_case_study_via_api(): void
    {
        CaseStudy::query()->create([
            'slug' => 'api-draft',
            'title' => 'API draft',
            'summary' => null,
            'body' => '<p>X</p>',
            'status' => CaseStudy::STATUS_DRAFT,
            'published_at' => null,
            'author_name' => 'Human',
            'author_type' => CaseStudy::AUTHOR_TYPE_HUMAN,
        ]);

        $this->getJson('/api/v1/case-studies/api-draft')->assertForbidden();
    }
}
