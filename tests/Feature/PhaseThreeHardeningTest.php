<?php

namespace Tests\Feature;

use App\Http\Controllers\Api\WebhookController;
use App\Models\User;
use App\Models\WebhookSubscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PhaseThreeHardeningTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_access_admin_api(): void
    {
        $this->getJson('/api/admin/settings')->assertUnauthorized();
    }

    public function test_vendor_cannot_access_admin_api(): void
    {
        $vendorUser = User::factory()->create(['role' => 'vendor']);
        $this->actingAs($vendorUser, 'api')
            ->getJson('/api/admin/settings')
            ->assertForbidden();
    }

    public function test_admin_can_access_admin_api(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin, 'api')
            ->getJson('/api/admin/settings')
            ->assertOk();
    }

    public function test_super_admin_can_access_admin_api(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);
        $this->actingAs($superAdmin, 'api')
            ->getJson('/api/admin/settings')
            ->assertOk();
    }

    public function test_restricted_admin_roles_cannot_access_admin_only_api(): void
    {
        $roles = ['procurement_manager', 'evaluator', 'auditor', 'verifikator'];

        foreach ($roles as $role) {
            $user = User::factory()->create(['role' => $role]);
            $this->actingAs($user, 'api')
                ->getJson('/api/admin/settings')
                ->assertForbidden();
        }
    }

    public function test_webhook_store_rejects_ssrf_targets(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $maliciousUrls = [
            'http://localhost/webhook',
            'http://localhost:8000/webhook',
            'http://127.0.0.1/webhook',
            'http://127.0.0.1:9000/webhook',
            'http://10.0.0.1/webhook',
            'http://172.16.0.1/webhook',
            'http://192.168.1.1/webhook',
            'http://169.254.169.254/latest/meta-data/',
            'http://[::1]/webhook',
            'file:///etc/passwd',
            'gopher://127.0.0.1:6379/_',
        ];

        foreach ($maliciousUrls as $url) {
            $response = $this->actingAs($admin, 'api')->postJson('/api/admin/webhooks', [
                'name' => 'SSRF Test',
                'url' => $url,
                'events' => ['tender.created'],
            ]);

            $response->assertUnprocessable();
        }
    }

    public function test_webhook_store_accepts_valid_public_url(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin, 'api')->postJson('/api/admin/webhooks', [
            'name' => 'Valid Webhook',
            'url' => 'https://example.com/webhook',
            'events' => ['tender.created'],
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('webhook_subscriptions', ['name' => 'Valid Webhook']);
    }

    public function test_webhook_secret_is_hidden_from_responses(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $storeResponse = $this->actingAs($admin, 'api')->postJson('/api/admin/webhooks', [
            'name' => 'Hidden Secret Webhook',
            'url' => 'https://example.com/webhook-secret',
            'events' => ['tender.created'],
        ]);

        $storeResponse->assertCreated();
        $this->assertArrayNotHasKey('secret', $storeResponse->json('data'));

        $indexResponse = $this->actingAs($admin, 'api')->getJson('/api/admin/webhooks');
        $indexResponse->assertOk();
        $this->assertArrayNotHasKey('secret', $indexResponse->json('data.0'));
    }

    public function test_webhook_dispatch_skips_ssrf_urls_and_does_not_call_internal_network(): void
    {
        Http::fake();

        $hook = WebhookSubscription::create([
            'name' => 'SSRF Internal Hook',
            'url' => 'http://127.0.0.1:8080/internal',
            'events' => ['tender.created'],
            'secret' => 'test-secret',
            'is_active' => true,
        ]);

        WebhookController::dispatch('tender.created', ['id' => 1]);

        Http::assertNothingSent();
    }
}
