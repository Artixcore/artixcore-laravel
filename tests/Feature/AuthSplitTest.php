<?php

namespace Tests\Feature;

use App\Models\AdminAccessRule;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthSplitTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_sees_admin_login_at_admin_login(): void
    {
        $this->get(route('admin.login'))->assertOk()->assertSee('Artixcore Admin', false);
    }

    public function test_guest_sees_master_login_at_master_login(): void
    {
        $this->get(route('master.login'))->assertOk()->assertSee('Master Admin Access', false);
    }

    public function test_admin_json_login_returns_redirect(): void
    {
        $this->seed();

        $res = $this->postJson(route('admin.login.submit'), [
            'email' => 'admin@artixcore.com',
            'password' => UserSeeder::PASSWORD,
        ]);

        $res->assertOk()->assertJsonPath('ok', true)->assertJsonPath('redirect', route('admin.dashboard'));
    }

    public function test_master_json_login_returns_redirect(): void
    {
        $this->seed();

        $res = $this->postJson(route('master.login.submit'), [
            'email' => 'master@artixcore.com',
            'password' => UserSeeder::PASSWORD,
        ]);

        $res->assertOk()->assertJsonPath('ok', true)->assertJsonPath('redirect', route('master.dashboard'));
    }

    public function test_admin_login_denied_for_end_user_credentials(): void
    {
        $this->seed();

        $this->postJson(route('admin.login.submit'), [
            'email' => 'user@artixcore.com',
            'password' => UserSeeder::PASSWORD,
        ])->assertStatus(422);
    }

    public function test_ip_rule_blocks_admin_login_when_rules_active(): void
    {
        $this->seed();

        AdminAccessRule::query()->create([
            'name' => 'test',
            'guard_area' => AdminAccessRule::AREA_ADMIN,
            'ip_address' => '203.0.113.50',
            'cidr' => null,
            'description' => null,
            'is_active' => true,
        ]);

        $this->get(route('admin.login'))->assertForbidden();
    }
}
