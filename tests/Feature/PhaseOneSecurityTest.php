<?php

namespace Tests\Feature;

use App\Models\Bid;
use App\Models\Contract;
use App\Models\ContractDelivery;
use App\Models\Tender;
use App\Models\TenderPayment;
use App\Models\TenderResult;
use App\Models\User;
use App\Models\Vendor;
use App\Services\PaymentGatewayService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Tests\TestCase;

class PhaseOneSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_refund_requires_authentication(): void
    {
        $this->postJson('/api/payment/refund/1')->assertUnauthorized();
    }

    public function test_vendor_and_unrelated_role_cannot_refund(): void
    {
        [$payment, $vendor] = $this->eligiblePayment();
        $this->actingAs($vendor['user'], 'api')->postJson("/api/payment/refund/{$payment->id}")->assertForbidden();
        $manager = User::factory()->create(['role' => 'procurement_manager']);
        $this->actingAs($manager, 'api')->postJson("/api/payment/refund/{$payment->id}")->assertForbidden();
    }

    public function test_admin_can_refund_eligible_losing_deposit_and_audit(): void
    {
        $this->assertEligibleRefundAllowed('admin');
    }

    public function test_super_admin_can_refund_eligible_losing_deposit_and_audit(): void
    {
        $this->assertEligibleRefundAllowed('super_admin');
    }

    public function test_refund_rejects_duplicate_and_unknown_payment(): void
    {
        [$payment] = $this->eligiblePayment(['status' => 'refunded', 'refunded_at' => now()]);
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin, 'api')->postJson("/api/payment/refund/{$payment->id}")->assertUnprocessable();
        $this->actingAs($admin, 'api')->postJson('/api/payment/refund/999999')->assertNotFound();
    }

    public function test_vendor_payment_summary_is_owned_and_hides_gateway_fields(): void
    {
        [$payment, $vendor] = $this->eligiblePayment();
        $other = $this->vendor();
        TenderPayment::create(['tender_id' => $payment->tender_id, 'vendor_id' => $other['vendor']->id, 'order_id' => 'OTHER-'.uniqid(), 'type' => 'deposit', 'amount' => 500000, 'status' => 'paid', 'snap_token' => 'secret', 'snap_url' => 'https://example.test', 'midtrans_data' => ['secret' => true]]);

        $response = $this->actingAs($vendor['user'], 'api')->getJson("/api/payment/tender/{$payment->tender_id}")->assertOk()->assertJsonCount(1, 'data.payments');
        $row = $response->json('data.payments.0');
        $this->assertSame($payment->id, $row['id']);
        $this->assertArrayNotHasKey('snap_token', $row);
        $this->assertArrayNotHasKey('snap_url', $row);
        $this->assertArrayNotHasKey('midtrans_data', $row);
    }

    public function test_unrelated_vendor_cannot_view_payment_summary_but_admin_roles_can(): void
    {
        [$payment] = $this->eligiblePayment();
        $other = $this->vendor();
        $this->actingAs($other['user'], 'api')->getJson("/api/payment/tender/{$payment->tender_id}")->assertNotFound();

        foreach (['admin', 'super_admin'] as $role) {
            $actor = User::factory()->create(['role' => $role]);
            $response = $this->actingAs($actor, 'api')->getJson("/api/payment/tender/{$payment->tender_id}")->assertOk();
            $this->assertArrayNotHasKey('snap_token', $response->json('data.payments.0'));
            $this->assertArrayNotHasKey('midtrans_data', $response->json('data.payments.0'));
        }
    }

    public function test_vendor_cannot_update_delivery_outside_url_contract(): void
    {
        $vendor = $this->vendor();
        [, $delivery] = $this->deliveryFor($vendor);
        [$otherContract] = $this->deliveryFor($vendor);

        $this->actingAs($vendor['user'], 'api')
            ->patchJson("/api/contracts/{$otherContract->id}/deliveries/{$delivery->id}/submit", ['vendor_notes' => 'Cross-contract update'])
            ->assertNotFound();
    }

    public function test_vendor_can_update_own_contract_delivery(): void
    {
        $vendor = $this->vendor();
        [$contract, $delivery] = $this->deliveryFor($vendor);

        $this->actingAs($vendor['user'], 'api')
            ->patchJson("/api/contracts/{$contract->id}/deliveries/{$delivery->id}/submit", ['vendor_notes' => 'Progress berjalan'])
            ->assertOk();

        $this->assertDatabaseHas('contract_deliveries', ['id' => $delivery->id, 'status' => 'in_progress', 'vendor_notes' => 'Progress berjalan']);
    }

    public function test_vendor_can_upload_valid_delivery_evidence_to_private_storage(): void
    {
        Storage::fake('local');
        $vendor = $this->vendor();
        [$contract, $delivery] = $this->deliveryFor($vendor);

        $this->actingAs($vendor['user'], 'api')
            ->patch("/api/contracts/{$contract->id}/deliveries/{$delivery->id}/submit", [
                'evidence' => UploadedFile::fake()->image('evidence.jpg'),
            ])
            ->assertOk();

        $path = $delivery->fresh()->evidence_path;
        $this->assertNotNull($path);
        Storage::disk('local')->assertExists($path);
    }

    public function test_delivery_evidence_rejects_invalid_mime_and_oversized_file(): void
    {
        $vendor = $this->vendor();
        [$contract, $delivery] = $this->deliveryFor($vendor);
        $url = "/api/contracts/{$contract->id}/deliveries/{$delivery->id}/submit";

        $this->actingAs($vendor['user'], 'api')
            ->patch($url, ['evidence' => UploadedFile::fake()->create('evidence.txt', 10, 'text/plain')])
            ->assertUnprocessable();

        $this->actingAs($vendor['user'], 'api')
            ->patch($url, ['evidence' => UploadedFile::fake()->create('evidence.png', 10241, 'image/png')])
            ->assertUnprocessable();
    }

    private function eligiblePayment(array $overrides = []): array
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $loser = $this->vendor();
        $winner = $this->vendor();
        $tender = Tender::create(['created_by' => $admin->id, 'title' => 'Tender', 'description' => 'Description', 'specification' => 'Specification', 'start_date' => now(), 'end_date' => now()->addMonth(), 'bidding_start' => now(), 'bidding_end' => now()->addWeek(), 'status' => 'finished']);
        $bid = Bid::create(['tender_id' => $tender->id, 'vendor_id' => $winner['vendor']->id, 'bid_amount' => 1000000, 'submitted_at' => now()]);
        TenderResult::create(['tender_id' => $tender->id, 'winner_vendor_id' => $winner['vendor']->id, 'winning_bid_id' => $bid->id, 'winning_bid_amount' => 1000000, 'selection_method' => 'lowest_price', 'decided_by' => $admin->id, 'decided_at' => now()]);
        $payment = TenderPayment::create(array_merge(['tender_id' => $tender->id, 'vendor_id' => $loser['vendor']->id, 'order_id' => 'PAYMENT-'.uniqid(), 'type' => 'deposit', 'amount' => 500000, 'status' => 'paid'], $overrides));

        return [$payment, $loser];
    }

    private function vendor(): array
    {
        $user = User::factory()->create(['role' => 'vendor']);
        $vendor = Vendor::create(['user_id' => $user->id, 'company_name' => fake()->company(), 'phone' => '081234567890', 'address' => fake()->address(), 'verification_status' => 'approved']);

        return compact('user', 'vendor');
    }

    private function deliveryFor(array $vendor): array
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $tender = Tender::create(['created_by' => $admin->id, 'title' => 'Tender Delivery', 'description' => 'Description', 'specification' => 'Specification', 'start_date' => now(), 'end_date' => now()->addMonth(), 'bidding_start' => now(), 'bidding_end' => now()->addWeek(), 'status' => 'finished']);
        $contract = Contract::create(['contract_number' => 'KONTRAK-'.uniqid(), 'tender_id' => $tender->id, 'vendor_id' => $vendor['vendor']->id, 'created_by' => $admin->id, 'status' => 'active', 'contract_value' => 1000000]);
        $delivery = ContractDelivery::create(['contract_id' => $contract->id, 'milestone_name' => 'Milestone', 'due_date' => now()->addWeek(), 'status' => 'scheduled']);

        return [$contract, $delivery];
    }

    private function assertEligibleRefundAllowed(string $role): void
    {
        [$payment] = $this->eligiblePayment();
        $actor = User::factory()->create(['role' => $role]);
        $gateway = Mockery::mock(PaymentGatewayService::class);
        $gateway->shouldReceive('refundDeposit')->once()->with($payment->id)->andReturn(['success' => true, 'message' => 'Refund berhasil diproses']);
        $this->app->instance(PaymentGatewayService::class, $gateway);

        $this->actingAs($actor, 'api')->postJson("/api/payment/refund/{$payment->id}")->assertOk();
        $this->assertDatabaseHas('activity_logs', ['user_id' => $actor->id, 'action' => 'payment_refunded', 'subject_type' => TenderPayment::class, 'subject_id' => $payment->id]);
    }
}
