<?php

namespace Tests\Feature;

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class RenewalProxyTest extends TestCase
{
    private string $token = '12.1767225600.0123456789abcdef0123456789abcdef01234567';

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('aporta.renewals_url', 'https://backend.test/api/v1/renewals');
        config()->set('aporta.intake_api_key', 'secret-key');
        config()->set('form.public', false);
    }

    public function test_it_proxies_the_prefill_with_the_intake_key(): void
    {
        Http::fake([
            'backend.test/*' => Http::response(['data' => ['shares_apartment' => false], 'meta' => ['reference_number' => 12]]),
        ]);

        $this->getJson("/api/form/renewal/{$this->token}")
            ->assertOk()
            ->assertJsonPath('meta.reference_number', 12)
            ->assertHeader('Cache-Control', 'no-store, private');

        Http::assertSent(fn (Request $request) => $request->url() === "https://backend.test/api/v1/renewals/{$this->token}"
            && $request->hasHeader('Authorization', 'Bearer secret-key'));
    }

    public function test_it_reports_an_expired_link(): void
    {
        Http::fake(['backend.test/*' => Http::response(['message' => 'gone'], 410)]);

        $this->getJson("/api/form/renewal/{$this->token}")->assertStatus(410);
    }

    public function test_it_rejects_malformed_tokens_without_calling_the_backend(): void
    {
        Http::fake();

        $this->getJson('/api/form/renewal/not-a-token')->assertStatus(410);

        Http::assertNothingSent();
    }

    public function test_it_reports_an_unreachable_backend(): void
    {
        Http::fake(['backend.test/*' => Http::response('boom', 500)]);

        $this->getJson("/api/form/renewal/{$this->token}")->assertStatus(503);
    }

    public function test_the_update_skips_the_password_gate_but_validates(): void
    {
        Http::fake();

        $this->putJson("/api/form/renewal/{$this->token}", [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['main_applicant', 'housing_wish']);

        Http::assertNothingSent();
    }
}
