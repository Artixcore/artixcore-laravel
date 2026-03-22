<?php

namespace Tests\Feature\Api;

use App\Models\Article;
use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContentApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_navigation_returns_menu_tree(): void
    {
        $this->seed();

        $res = $this->getJson('/api/v1/navigation?menu=primary');

        $res->assertOk()
            ->assertJsonPath('data.menu', 'primary')
            ->assertJsonStructure([
                'data' => [
                    'menu',
                    'items' => [
                        '*' => ['id', 'label', 'href', 'feature', 'children'],
                    ],
                ],
            ]);
    }

    public function test_home_page_by_path(): void
    {
        $this->seed();

        $res = $this->getJson('/api/v1/pages/home');

        $res->assertOk()
            ->assertJsonPath('data.path', 'home')
            ->assertJsonPath('data.href', '/');
    }

    public function test_articles_index_and_show(): void
    {
        $this->seed();

        $this->getJson('/api/v1/articles')->assertOk();

        $this->getJson('/api/v1/articles/building-spl-token-programs')
            ->assertOk()
            ->assertJsonPath('data.slug', 'building-spl-token-programs');
    }

    public function test_trending_requires_type(): void
    {
        $this->seed();

        $this->getJson('/api/v1/trending')->assertStatus(422);

        $this->getJson('/api/v1/trending?type=articles')->assertOk();
    }

    public function test_related_endpoint(): void
    {
        $this->seed();

        $this->getJson('/api/v1/related?type=article&slug=building-spl-token-programs')
            ->assertOk()
            ->assertJsonStructure(['type', 'slug', 'related']);
    }

    public function test_draft_article_forbidden(): void
    {
        Article::query()->create([
            'slug' => 'draft-only',
            'title' => 'Draft',
            'status' => 'draft',
        ]);

        $this->getJson('/api/v1/articles/draft-only')->assertForbidden();
    }

    public function test_draft_page_forbidden(): void
    {
        Page::query()->create([
            'path' => 'secret',
            'title' => 'Secret',
            'status' => 'draft',
        ]);

        $this->getJson('/api/v1/pages/secret')->assertForbidden();
    }
}
