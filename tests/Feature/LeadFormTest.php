<?php

namespace Tests\Feature;

use App\Models\Lead;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class LeadFormTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Domain must pass {@see StoreWebLeadRequest} email:rfc,dns in CI/local resolvers.
     */
    private const VALID_TEST_EMAIL = 'jane@gmail.com';

    public function test_lead_page_renders(): void
    {
        $response = $this->get(route('lead.create'));

        $response->assertOk()
            ->assertSee('Start Your Project with Artixcore', false);
    }

    public function test_lead_page_includes_turnstile_markup_when_site_key_configured(): void
    {
        config([
            'captcha.driver' => 'turnstile',
            'captcha.bypass' => false,
            'services.turnstile.bypass' => false,
            'services.turnstile.site_key' => 'test-site-key-public',
        ]);

        $response = $this->get(route('lead.create'));

        $response->assertOk()
            ->assertSee('id="lead-turnstile"', false)
            ->assertSee('test-site-key-public', false)
            ->assertSee('cf-turnstile', false);
    }

    public function test_lead_validation_requires_fields(): void
    {
        $this->get(route('lead.create'));

        $response = $this->post(route('lead.store'), [
            '_token' => csrf_token(),
        ]);

        $response->assertSessionHasErrors(['name', 'email', 'service_type', 'message']);
    }

    public function test_lead_rejects_invalid_email(): void
    {
        $this->get(route('lead.create'));

        $response = $this->post(route('lead.store'), [
            '_token' => csrf_token(),
            'name' => 'Test User',
            'email' => 'not-an-email',
            'service_type' => Lead::SERVICE_TYPES[0],
            'message' => 'This is a valid length message for testing.',
            'source' => 'website',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_lead_stores_row_when_captcha_bypassed_in_testing(): void
    {
        $this->get(route('lead.create'));

        $response = $this->post(route('lead.store'), [
            '_token' => csrf_token(),
            'name' => 'Jane Doe',
            'email' => self::VALID_TEST_EMAIL,
            'phone' => '+1 555 0100',
            'service_type' => 'Web Development',
            'message' => 'We need a Laravel application with billing and admin.',
            'source' => 'website',
        ]);

        $response->assertRedirect(route('lead.create'));
        $response->assertSessionHas('status');

        $this->assertDatabaseHas('leads', [
            'email' => self::VALID_TEST_EMAIL,
            'service_type' => 'Web Development',
            'status' => Lead::STATUS_NEW,
            'source' => 'website',
        ]);

        $lead = Lead::query()->where('email', self::VALID_TEST_EMAIL)->first();
        $this->assertNotNull($lead);
        $this->assertNotNull($lead->submitted_at);
        $this->assertStringContainsString('Laravel', (string) $lead->message);
    }

    public function test_lead_json_validation_returns_422(): void
    {
        $this->get(route('lead.create'));

        $response = $this->postJson(route('lead.store'), [
            '_token' => csrf_token(),
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('ok', false)
            ->assertJsonStructure(['message', 'errors']);
    }

    public function test_lead_json_success_with_fake_turnstile_verification(): void
    {
        config([
            'captcha.bypass' => false,
            'captcha.driver' => 'turnstile',
            'captcha.turnstile.secret_key' => 'test-secret',
        ]);

        Http::fake([
            (string) config('captcha.turnstile.verify_url') => Http::response(['success' => true], 200),
        ]);

        $this->get(route('lead.create'));

        $response = $this->postJson(route('lead.store'), [
            '_token' => csrf_token(),
            'name' => 'Ajax User',
            'email' => self::VALID_TEST_EMAIL,
            'service_type' => 'Web Development',
            'message' => 'This is at least ten chars for the message body.',
            'source' => 'website',
            'cf-turnstile-response' => 'test-turnstile-token',
        ]);

        $response->assertCreated()
            ->assertJsonPath('ok', true)
            ->assertJsonPath('data.lead.email', self::VALID_TEST_EMAIL);

        $this->assertDatabaseHas('leads', [
            'email' => self::VALID_TEST_EMAIL,
            'name' => 'Ajax User',
        ]);
    }

    public function test_lead_json_captcha_failure_returns_422(): void
    {
        config([
            'captcha.bypass' => false,
            'captcha.driver' => 'turnstile',
            'captcha.turnstile.secret_key' => 'test-secret',
        ]);

        Http::fake([
            (string) config('captcha.turnstile.verify_url') => Http::response(['success' => false], 200),
        ]);

        $this->get(route('lead.create'));

        $response = $this->postJson(route('lead.store'), [
            '_token' => csrf_token(),
            'name' => 'Ajax User',
            'email' => self::VALID_TEST_EMAIL,
            'service_type' => 'Web Development',
            'message' => 'This is at least ten chars for the message body.',
            'cf-turnstile-response' => 'bad-token',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('ok', false)
            ->assertJsonStructure(['errors' => ['captcha', 'cf-turnstile-response']]);
    }

    public function test_lead_json_missing_turnstile_token_returns_422(): void
    {
        config([
            'captcha.bypass' => false,
            'captcha.driver' => 'turnstile',
            'captcha.turnstile.secret_key' => 'test-secret',
        ]);

        $this->get(route('lead.create'));

        $response = $this->postJson(route('lead.store'), [
            '_token' => csrf_token(),
            'name' => 'Ajax User',
            'email' => self::VALID_TEST_EMAIL,
            'service_type' => 'Web Development',
            'message' => 'This is at least ten chars for the message body.',
            'source' => 'website',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('ok', false)
            ->assertJsonValidationErrors(['cf-turnstile-response']);
    }

    public function test_lead_json_missing_turnstile_secret_returns_422_not_500(): void
    {
        config([
            'captcha.bypass' => false,
            'captcha.driver' => 'turnstile',
            'services.turnstile.secret_key' => '',
            'captcha.turnstile.secret_key' => '',
        ]);

        Http::fake();

        $this->get(route('lead.create'));

        $response = $this->postJson(route('lead.store'), [
            '_token' => csrf_token(),
            'name' => 'Ajax User',
            'email' => self::VALID_TEST_EMAIL,
            'service_type' => 'Web Development',
            'message' => 'This is at least ten chars for the message body.',
            'source' => 'website',
            'cf-turnstile-response' => 'any-token',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('ok', false)
            ->assertJsonStructure(['errors' => ['captcha', 'cf-turnstile-response']]);

        Http::assertNothingSent();
    }

    public function test_lead_json_rate_limit_returns_429(): void
    {
        config([
            'rate_limiting.forms_per_minute' => 2,
            'captcha.bypass' => false,
            'captcha.driver' => 'turnstile',
            'captcha.turnstile.secret_key' => 'test-secret',
        ]);

        Http::fake([
            (string) config('captcha.turnstile.verify_url') => Http::response(['success' => true], 200),
        ]);

        $this->get(route('lead.create'));

        $payload = [
            '_token' => csrf_token(),
            'name' => 'Throttle User',
            'email' => self::VALID_TEST_EMAIL,
            'service_type' => 'Web Development',
            'message' => 'This is at least ten chars for the message body.',
            'source' => 'website',
            'cf-turnstile-response' => 'test-turnstile-token',
        ];

        $this->postJson(route('lead.store'), $payload)->assertCreated();
        $this->postJson(route('lead.store'), $payload)->assertCreated();

        $this->postJson(route('lead.store'), $payload)->assertStatus(429);
    }

    public function test_non_ajax_post_redirects_with_success_without_accept_json(): void
    {
        $this->get(route('lead.create'));

        $response = $this->post(route('lead.store'), [
            '_token' => csrf_token(),
            'name' => 'Classic User',
            'email' => self::VALID_TEST_EMAIL,
            'service_type' => 'Web Development',
            'message' => 'Classic form submission without Accept JSON header.',
            'source' => 'website',
        ]);

        $response->assertRedirect(route('lead.create'));
        $response->assertSessionHas('status');
    }
}
