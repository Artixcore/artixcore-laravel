<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use App\Services\Content\VideoEmbedResolver;
use App\Services\HtmlSanitizer;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticlePublishingTest extends TestCase
{
    use RefreshDatabase;

    protected function actingAsMasterAdmin(): User
    {
        $this->seed([RoleSeeder::class, RolePermissionSeeder::class]);
        $user = User::factory()->create(['user_kind' => 'internal']);
        $user->assignRole('master_admin');

        return $user;
    }

    public function test_public_articles_index_loads(): void
    {
        $this->get(route('articles.index'))->assertOk();
    }

    public function test_legacy_resources_article_url_redirects(): void
    {
        $this->get('/resources/articles')->assertRedirect('/articles');
    }

    public function test_legacy_resources_case_studies_url_redirects(): void
    {
        $this->get('/resources/case-studies')->assertRedirect('/case-studies');
    }

    public function test_blog_index_loads_same_as_articles_stack(): void
    {
        $this->get(route('blog.index'))->assertOk();
    }

    public function test_published_article_detail_loads(): void
    {
        Article::query()->create([
            'slug' => 'hello-world',
            'title' => 'Hello world',
            'summary' => 'Summary',
            'body' => '<p>Body</p>',
            'status' => Article::STATUS_PUBLISHED,
            'published_at' => now()->subDay(),
            'author_name' => 'Ali 1.0',
            'author_type' => Article::AUTHOR_TYPE_AI,
        ]);

        $this->get(route('articles.show', 'hello-world'))->assertOk();
    }

    public function test_draft_article_not_public(): void
    {
        Article::query()->create([
            'slug' => 'secret-draft',
            'title' => 'Draft',
            'summary' => null,
            'body' => '<p>X</p>',
            'status' => Article::STATUS_DRAFT,
            'published_at' => null,
            'author_name' => 'Human',
            'author_type' => Article::AUTHOR_TYPE_HUMAN,
        ]);

        $this->get(route('articles.show', 'secret-draft'))->assertNotFound();
    }

    public function test_published_without_publish_timestamp_not_public(): void
    {
        Article::query()->create([
            'slug' => 'scheduled-ish',
            'title' => 'No timestamp',
            'summary' => 'S',
            'body' => '<p>X</p>',
            'status' => Article::STATUS_PUBLISHED,
            'published_at' => null,
            'author_name' => 'Human',
            'author_type' => Article::AUTHOR_TYPE_HUMAN,
        ]);

        $this->get(route('articles.show', 'scheduled-ish'))->assertNotFound();
        $this->get(route('articles.index'))->assertOk();
    }

    public function test_archived_article_not_public(): void
    {
        Article::query()->create([
            'slug' => 'old-archived',
            'title' => 'Archived',
            'summary' => null,
            'body' => '<p>X</p>',
            'status' => Article::STATUS_ARCHIVED,
            'published_at' => now()->subYear(),
            'author_name' => 'Human',
            'author_type' => Article::AUTHOR_TYPE_HUMAN,
        ]);

        $this->get(route('articles.show', 'old-archived'))->assertNotFound();
    }

    public function test_guest_cannot_access_article_admin(): void
    {
        $this->get(route('admin.articles.index'))->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_open_article_index(): void
    {
        $user = $this->actingAsMasterAdmin();

        $this->actingAs($user)->get(route('admin.articles.index'))->assertOk();
    }

    public function test_video_resolver_allowlists_providers(): void
    {
        $r = app(VideoEmbedResolver::class);

        $this->assertNotNull($r->resolve('https://www.youtube.com/watch?v=dQw4w9WgXcQ'));
        $this->assertNull($r->resolve('https://evil.example/embed/foo'));
    }

    public function test_html_sanitizer_strips_scripts(): void
    {
        $html = app(HtmlSanitizer::class)->sanitize('<p>ok</p><script>alert(1)</script>');
        $this->assertStringNotContainsString('<script>', $html);
    }

    public function test_generate_ai_command_dry_run_does_not_require_api_key(): void
    {
        $this->artisan('articles:generate-ai', ['--dry-run' => true])->assertExitCode(0);
        $this->artisan('articles:generate-ai', ['--dry-run' => true, '--count' => 2])->assertExitCode(0);
    }

    public function test_content_generate_ai_dry_run(): void
    {
        $this->artisan('content:generate-ai', ['--dry-run' => true])->assertExitCode(0);
        $this->artisan('content:generate-ai', ['--dry-run' => true, '--only' => 'articles'])->assertExitCode(0);
    }
}
