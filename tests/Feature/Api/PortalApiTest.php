<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortalApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_portal_login_and_me(): void
    {
        $this->seed();

        $login = $this->postJson('/api/v1/auth/login', [
            'email' => 'portal@example.com',
            'password' => 'password',
        ]);

        $login->assertOk()
            ->assertJsonStructure(['data' => ['token', 'token_type', 'user']]);

        $token = $login->json('data.token');
        $this->assertNotEmpty($token);

        $this->getJson('/api/v1/portal/me', [
            'Authorization' => 'Bearer '.$token,
        ])
            ->assertOk()
            ->assertJsonPath('data.user.email', 'portal@example.com')
            ->assertJsonPath('data.user.user_kind', 'external');
    }

    public function test_master_admin_cannot_use_portal_login_without_portal_permission(): void
    {
        $this->seed();

        $this->postJson('/api/v1/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ])->assertStatus(403);
    }

    public function test_portal_user_can_be_granted_portal_access_via_permission_sync(): void
    {
        $this->seed();

        /** @var User $master */
        $master = User::query()->where('email', 'test@example.com')->firstOrFail();
        $master->givePermissionTo('portal.access');

        $login = $this->postJson('/api/v1/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $login->assertOk();
        $token = $login->json('data.token');

        $this->getJson('/api/v1/portal/me', [
            'Authorization' => 'Bearer '.$token,
        ])->assertOk();
    }
}
