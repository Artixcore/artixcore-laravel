<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    use RefreshDatabase;

    private const VALID_EMAIL = 'jane@gmail.com';

    public function test_lead_honeypot_returns_success_without_persisting(): void
    {
        $this->get(route('lead'));

        $response = $this->postJson(route('lead.store'), [
            '_token' => csrf_token(),
            'name' => 'Bot',
            'email' => self::VALID_EMAIL,
            'service_type' => Lead::SERVICE_TYPES[0],
            'message' => 'This message is long enough for validation.',
            'website' => 'http://spam.example',
        ]);

        $response->assertOk()->assertJsonPath('ok', true);

        $this->assertDatabaseMissing('leads', [
            'email' => self::VALID_EMAIL,
            'name' => 'Bot',
        ]);
    }

    public function test_portal_me_requires_authentication(): void
    {
        $this->getJson('/api/v1/portal/me')->assertStatus(401);
    }

    public function test_admin_leads_redirects_guests(): void
    {
        $this->get(route('admin.leads.index'))->assertRedirect(route('login'));
    }

    public function test_public_pages_include_security_headers(): void
    {
        $this->get('/')->assertOk()
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('Content-Security-Policy');
    }

    public function test_admin_lead_show_escapes_html_in_message(): void
    {
        $this->seed();

        /** @var User $admin */
        $admin = User::query()->where('email', 'master@artixcore.com')->firstOrFail();

        $lead = Lead::query()->create([
            'source' => 'website',
            'status' => Lead::STATUS_NEW,
            'name' => 'Tester',
            'email' => 'tester@example.com',
            'service_type' => Lead::SERVICE_TYPES[0],
            'message' => '<script>alert(1)</script><p>Hello</p>',
            'submitted_at' => now(),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.leads.show', $lead));

        $response->assertOk()
            ->assertDontSee('<script>alert(1)</script>', false);
    }

    public function test_admin_leads_index_accepts_invalid_sort_gracefully(): void
    {
        $this->seed();

        /** @var User $admin */
        $admin = User::query()->where('email', 'master@artixcore.com')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.leads.index', ['sort' => 'injected', 'direction' => 'desc']))
            ->assertOk();
    }

    public function test_blade_login_throttles_after_repeated_failures(): void
    {
        $this->get(route('login'));

        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', [
                '_token' => csrf_token(),
                'email' => 'nobody@example.com',
                'password' => 'wrong-password',
            ]);
        }

        $sixth = $this->post('/login', [
            '_token' => csrf_token(),
            'email' => 'nobody@example.com',
            'password' => 'wrong-password',
        ]);

        $sixth->assertStatus(429);
    }
}
