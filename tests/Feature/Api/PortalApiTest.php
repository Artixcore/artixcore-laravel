<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortalApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_portal_login_and_me(): void
    {
        $this->seed();

        $login = $this->postJson('/api/v1/auth/login', [
            'email' => 'client@artixcore.com',
            'password' => UserSeeder::PASSWORD,
        ]);

        $login->assertOk()
            ->assertJsonStructure(['data' => ['token', 'token_type', 'user']]);

        $token = $login->json('data.token');
        $this->assertNotEmpty($token);

        $this->getJson('/api/v1/portal/me', [
            'Authorization' => 'Bearer '.$token,
        ])
            ->assertOk()
            ->assertJsonPath('data.user.email', 'client@artixcore.com')
            ->assertJsonPath('data.user.user_kind', 'external')
            ->assertJsonStructure([
                'data' => [
                    'user' => ['id', 'aid', 'name', 'email', 'user_kind', 'phone', 'bio', 'designation'],
                    'avatar_url',
                    'roles',
                    'permissions',
                ],
            ]);
    }

    public function test_master_admin_cannot_use_portal_login_without_portal_permission(): void
    {
        $this->seed();

        $this->postJson('/api/v1/auth/login', [
            'email' => 'master@artixcore.com',
            'password' => UserSeeder::PASSWORD,
        ])->assertStatus(403);
    }

    public function test_portal_user_can_be_granted_portal_access_via_permission_sync(): void
    {
        $this->seed();

        /** @var User $master */
        $master = User::query()->where('email', 'master@artixcore.com')->firstOrFail();
        $master->givePermissionTo('portal.access');

        $login = $this->postJson('/api/v1/auth/login', [
            'email' => 'master@artixcore.com',
            'password' => UserSeeder::PASSWORD,
        ]);

        $login->assertOk();
        $token = $login->json('data.token');

        $this->getJson('/api/v1/portal/me', [
            'Authorization' => 'Bearer '.$token,
        ])->assertOk();
    }
}
