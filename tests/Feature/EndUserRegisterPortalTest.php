<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EndUserRegisterPortalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_guest_can_view_register_page(): void
    {
        $this->get(route('register'))
            ->assertOk()
            ->assertSee('Create account', false)
            ->assertHeader('X-Robots-Tag', 'noindex, nofollow, noarchive');
    }

    public function test_register_creates_end_user_and_redirects_to_portal(): void
    {
        $response = $this->post(route('register.submit'), [
            'name' => 'Portal Tester',
            'email' => 'portal-tester@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'company_name' => 'Test Co',
            'phone' => '+15551234567',
            'terms' => '1',
        ]);

        $response->assertRedirect(route('portal'));

        $user = User::query()->where('email', 'portal-tester@example.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->hasRole('end_user'));
        $this->assertTrue($user->can('portal.access'));

        $this->actingAs($user)->get(route('portal'))
            ->assertOk()
            ->assertHeader('X-Robots-Tag', 'noindex, nofollow, noarchive')
            ->assertSee('Portal Tester', false);
    }

    public function test_end_user_cannot_access_admin_dashboard(): void
    {
        $user = User::factory()->create([
            'email' => 'endonly@example.com',
            'user_kind' => 'external',
        ]);
        $user->assignRole('end_user');

        $this->actingAs($user)->get(route('admin.dashboard'))->assertForbidden();
    }

    public function test_login_page_has_noindex_header(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertHeader('X-Robots-Tag', 'noindex, nofollow, noarchive');
    }

    public function test_sitemap_excludes_auth_paths(): void
    {
        $xml = $this->get(route('sitemap'))->assertOk()->getContent();
        $this->assertStringNotContainsString('/login', $xml);
        $this->assertStringNotContainsString('/register', $xml);
        $this->assertStringNotContainsString('/portal', $xml);
        $this->assertStringNotContainsString('/admin/', $xml);
    }
}
