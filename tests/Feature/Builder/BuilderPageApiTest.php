<?php

namespace Tests\Feature\Builder;

use App\Models\Page;
use App\Models\PageBlock;
use App\Models\User;
use App\Support\Builder\BuilderDocumentDefaults;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BuilderPageApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Builder routes use the `web` stack; pipeline resolves PreventRequestForgery (not deprecated ValidateCsrfToken).
        $this->withoutMiddleware(PreventRequestForgery::class);
    }

    private function contentAdmin(): User
    {
        $this->seed(RolePermissionSeeder::class);
        $user = User::factory()->create(['user_kind' => 'internal']);
        $user->assignRole('content_admin');

        return $user;
    }

    public function test_guest_cannot_access_builder_api(): void
    {
        $page = Page::query()->create([
            'path' => 't-guest-'.uniqid(),
            'title' => 'T',
            'status' => 'draft',
        ]);

        $this->getJson('/builder-api/v1/pages/'.$page->id)
            ->assertUnauthorized();
    }

    public function test_authenticated_builder_can_load_and_save_document(): void
    {
        $user = $this->contentAdmin();
        $page = Page::query()->create([
            'path' => 't-save-'.uniqid(),
            'title' => 'Save me',
            'status' => 'draft',
        ]);

        $load = $this->actingAs($user)
            ->getJson('/builder-api/v1/pages/'.$page->id)
            ->assertOk();
        $this->assertNotNull($load->json('data.latest_version_id'));

        $doc = BuilderDocumentDefaults::emptyDocument();
        $doc['root']['children'][] = [
            'id' => '00000000-0000-4000-8000-000000000001',
            'type' => 'hero',
            'version' => 1,
            'props' => [
                'eyebrow' => 'E',
                'title' => 'T',
                'subtitle' => 'S',
                'primaryCta' => ['label' => 'Go', 'href' => '/c'],
                'secondaryCta' => ['label' => 'N', 'href' => '#'],
            ],
            'children' => [],
        ];

        $first = $this->actingAs($user)
            ->putJson('/builder-api/v1/pages/'.$page->id.'/document', [
                'document' => $doc,
                'label' => 'test',
            ])
            ->assertOk()
            ->json('data.latest_version_id');

        $this->actingAs($user)
            ->putJson('/builder-api/v1/pages/'.$page->id.'/document', [
                'document' => $doc,
                'base_version_id' => $first,
                'label' => 'test2',
            ])
            ->assertOk();
    }

    public function test_publish_compiles_page_blocks(): void
    {
        $user = $this->contentAdmin();
        $page = Page::query()->create([
            'path' => 't-pub-'.uniqid(),
            'title' => 'Pub',
            'status' => 'draft',
        ]);

        $doc = BuilderDocumentDefaults::emptyDocument();
        $doc['root']['children'][] = [
            'id' => '00000000-0000-4000-8000-000000000002',
            'type' => 'cta',
            'version' => 1,
            'props' => [
                'title' => 'X',
                'body' => 'Y',
                'buttonLabel' => 'Z',
                'href' => '/z',
            ],
            'children' => [],
        ];

        $this->actingAs($user)
            ->putJson('/builder-api/v1/pages/'.$page->id.'/document', ['document' => $doc])
            ->assertOk();

        $this->actingAs($user)
            ->postJson('/builder-api/v1/pages/'.$page->id.'/publish')
            ->assertOk();

        $page->refresh();
        $this->assertSame('published', $page->status);
        $this->assertSame(1, PageBlock::query()->where('page_id', $page->id)->count());
        $this->assertSame('cta', PageBlock::query()->where('page_id', $page->id)->value('type'));
    }

    public function test_invalid_document_returns_422(): void
    {
        $user = $this->contentAdmin();
        $page = Page::query()->create([
            'path' => 't-inv-'.uniqid(),
            'title' => 'Inv',
            'status' => 'draft',
        ]);

        $this->actingAs($user)
            ->putJson('/builder-api/v1/pages/'.$page->id.'/document', [
                'document' => ['schemaVersion' => 99, 'root' => []],
            ])
            ->assertStatus(422);
    }
}
