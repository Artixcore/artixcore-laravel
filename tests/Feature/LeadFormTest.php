<?php

namespace Tests\Feature;

use App\Models\Lead;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_lead_page_renders(): void
    {
        $response = $this->get(route('lead'));

        $response->assertOk()
            ->assertSee('Start Your Project with Artixcore', false);
    }

    public function test_lead_validation_requires_fields(): void
    {
        $this->get(route('lead'));

        $response = $this->post(route('lead.store'), [
            '_token' => csrf_token(),
        ]);

        $response->assertSessionHasErrors(['name', 'email', 'service_type', 'message']);
    }

    public function test_lead_rejects_invalid_email(): void
    {
        $this->get(route('lead'));

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
        $this->get(route('lead'));

        $response = $this->post(route('lead.store'), [
            '_token' => csrf_token(),
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '+1 555 0100',
            'service_type' => 'Web Development',
            'message' => 'We need a Laravel application with billing and admin.',
            'source' => 'website',
        ]);

        $response->assertRedirect(route('lead'));
        $response->assertSessionHas('status');

        $this->assertDatabaseHas('leads', [
            'email' => 'jane@example.com',
            'service_type' => 'Web Development',
            'status' => Lead::STATUS_NEW,
            'source' => 'website',
        ]);

        $lead = Lead::query()->where('email', 'jane@example.com')->first();
        $this->assertNotNull($lead);
        $this->assertNotNull($lead->submitted_at);
        $this->assertStringContainsString('Laravel', (string) $lead->message);
    }
}
