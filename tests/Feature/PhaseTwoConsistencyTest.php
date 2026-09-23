<?php

namespace Tests\Feature;

use App\Models\Bid;
use App\Models\Contract;
use App\Models\Tender;
use App\Models\TenderItem;
use App\Models\TenderParticipant;
use App\Models\TenderPayment;
use App\Models\TenderResult;
use App\Models\User;
use App\Models\Vendor;
use App\Services\AiPricePredictionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PhaseTwoConsistencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_settlement_callback_handles_deposit_paid_without_legacy_model(): void
    {
        $vendor = $this->vendor();
        $admin = User::factory()->create(['role' => 'admin']);
        $tender = $this->createTender($admin);

        $payment = TenderPayment::create([
            'tender_id' => $tender->id,
            'vendor_id' => $vendor['vendor']->id,
            'order_id' => 'DEPOSIT-'.$tender->id.'-'.$vendor['vendor']->id.'-123',
            'type' => 'deposit',
            'amount' => 500000,
            'status' => 'pending',
        ]);

        $serverKey = config('services.midtrans.server_key') ?: 'SB-Mid-server-TESTKEY123';
        $signature = hash('sha512', $payment->order_id.'200'.'500000.00'.$serverKey);

        $response = $this->postJson('/api/payment/notification', [
            'order_id' => $payment->order_id,
            'status_code' => '200',
            'gross_amount' => '500000.00',
            'transaction_status' => 'settlement',
            'signature_key' => $signature,
        ]);

        $response->assertOk();
        $this->assertSame('paid', $payment->fresh()->status);
    }

    public function test_ai_price_prediction_uses_active_bid_and_tender_results(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        for ($i = 0; $i < 3; $i++) {
            $tender = Tender::create([
                'created_by' => $admin->id,
                'title' => 'Tender '.$i,
                'description' => 'Desc',
                'specification' => 'Spec',
                'open_bidding_price' => 100000000,
                'start_date' => now()->subMonths(2),
                'end_date' => now()->subMonth(),
                'bidding_start' => now()->subMonths(2),
                'bidding_end' => now()->subMonth(),
                'status' => 'finished',
            ]);

            $vendor = $this->vendor();
            $bid = Bid::create([
                'tender_id' => $tender->id,
                'vendor_id' => $vendor['vendor']->id,
                'bid_amount' => 85000000 + ($i * 1000000),
                'submitted_at' => now()->subMonth(),
            ]);

            TenderResult::create([
                'tender_id' => $tender->id,
                'winner_vendor_id' => $vendor['vendor']->id,
                'winning_bid_id' => $bid->id,
                'winning_bid_amount' => $bid->bid_amount,
                'selection_method' => 'lowest_price',
                'decided_by' => $admin->id,
                'decided_at' => now()->subMonth(),
            ]);
        }

        $service = app(AiPricePredictionService::class);
        $result = $service->predictPrice('konstruksi', 100000000);

        $this->assertSame('linear_regression', $result['method']);
        $this->assertGreaterThanOrEqual(3, $result['data_points']);
    }

    public function test_ai_detect_anomaly_and_scoring_use_active_bid_domain(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $tender = $this->createTender($admin, ['status' => 'bidding', 'hps' => 100000000]);

        $v1 = $this->vendor();
        $v2 = $this->vendor();

        Bid::create(['tender_id' => $tender->id, 'vendor_id' => $v1['vendor']->id, 'bid_amount' => 80000000, 'submitted_at' => now()]);
        Bid::create(['tender_id' => $tender->id, 'vendor_id' => $v2['vendor']->id, 'bid_amount' => 85000000, 'submitted_at' => now()]);

        $service = app(AiPricePredictionService::class);
        $anomaly = $service->detectAnomaly($tender->id, 82000000);
        $this->assertArrayHasKey('anomaly_score', $anomaly);

        $scores = $service->scoreVendors($tender->id);
        $this->assertCount(2, $scores);
        $this->assertSame(80000000.0, (float) $scores[0]['bid_price']);
    }

    public function test_contract_store_and_complete_succeed_without_ghost_columns(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $vendor = $this->vendor();
        $tender = $this->createTender($admin, ['status' => 'finished']);
        $bid = Bid::create(['tender_id' => $tender->id, 'vendor_id' => $vendor['vendor']->id, 'bid_amount' => 1000000, 'submitted_at' => now()]);
        TenderResult::create([
            'tender_id' => $tender->id,
            'winner_vendor_id' => $vendor['vendor']->id,
            'winning_bid_id' => $bid->id,
            'winning_bid_amount' => 1000000,
            'selection_method' => 'lowest_price',
            'decided_by' => $admin->id,
            'decided_at' => now(),
        ]);

        $storeResponse = $this->actingAs($admin, 'api')->postJson('/api/admin/contracts', [
            'tender_id' => $tender->id,
            'contract_value' => 1000000,
            'notes' => 'Catatan kontrak',
        ]);
        $storeResponse->assertCreated();
        $contractId = $storeResponse->json('data.id');

        $contract = Contract::findOrFail($contractId);
        $contract->update(['status' => 'active']);

        $completeResponse = $this->actingAs($admin, 'api')->patchJson("/api/admin/contracts/{$contract->id}/complete");
        $completeResponse->assertOk();
        $this->assertSame('completed', $contract->fresh()->status);
    }

    public function test_bid_submission_without_items_succeeds_and_creates_no_bid_items(): void
    {
        $vendor = $this->vendor();
        $admin = User::factory()->create(['role' => 'admin']);
        $tender = $this->createTender($admin, ['status' => 'bidding']);
        TenderParticipant::create(['tender_id' => $tender->id, 'vendor_id' => $vendor['vendor']->id, 'joined_at' => now()]);

        $response = $this->actingAs($vendor['user'], 'api')->postJson("/api/tenders/{$tender->id}/penawaran", [
            'bid_amount' => 50000000,
            'notes' => 'Penawaran tanpa items',
        ]);

        $response->assertCreated();
        $bidId = $response->json('data.id');
        $this->assertDatabaseHas('bids', ['id' => $bidId, 'bid_amount' => 50000000]);
        $this->assertDatabaseCount('bid_items', 0);
    }

    public function test_bid_submission_with_valid_items_persists_bid_items_and_subtotals(): void
    {
        $vendor = $this->vendor();
        $admin = User::factory()->create(['role' => 'admin']);
        $tender = $this->createTender($admin, ['status' => 'bidding']);
        TenderParticipant::create(['tender_id' => $tender->id, 'vendor_id' => $vendor['vendor']->id, 'joined_at' => now()]);

        $item1 = TenderItem::create(['tender_id' => $tender->id, 'description' => 'Kabel LAN', 'unit' => 'meter', 'quantity' => 10, 'hps_unit_price' => 5000]);
        $item2 = TenderItem::create(['tender_id' => $tender->id, 'description' => 'Switch', 'unit' => 'unit', 'quantity' => 2, 'hps_unit_price' => 1000000]);

        $response = $this->actingAs($vendor['user'], 'api')->postJson("/api/tenders/{$tender->id}/penawaran", [
            'bid_amount' => 2050000,
            'items' => [
                ['tender_item_id' => $item1->id, 'unit_price' => 4500],
                ['tender_item_id' => $item2->id, 'unit_price' => 1000000],
            ],
        ]);

        $response->assertCreated();
        $bidId = $response->json('data.id');
        $this->assertDatabaseHas('bid_items', [
            'bid_id' => $bidId,
            'tender_item_id' => $item1->id,
            'unit_price' => 4500,
            'subtotal' => 45000,
        ]);
        $this->assertDatabaseHas('bid_items', [
            'bid_id' => $bidId,
            'tender_item_id' => $item2->id,
            'unit_price' => 1000000,
            'subtotal' => 2000000,
        ]);
    }

    public function test_bid_submission_rejects_invalid_item_reference(): void
    {
        $vendor = $this->vendor();
        $admin = User::factory()->create(['role' => 'admin']);
        $tender = $this->createTender($admin, ['status' => 'bidding']);
        TenderParticipant::create(['tender_id' => $tender->id, 'vendor_id' => $vendor['vendor']->id, 'joined_at' => now()]);

        $this->actingAs($vendor['user'], 'api')->postJson("/api/tenders/{$tender->id}/penawaran", [
            'bid_amount' => 5000000,
            'items' => [
                ['tender_item_id' => 999999, 'unit_price' => 1000],
            ],
        ])->assertUnprocessable();
    }

    public function test_bid_submission_rejects_duplicate_items(): void
    {
        $vendor = $this->vendor();
        $admin = User::factory()->create(['role' => 'admin']);
        $tender = $this->createTender($admin, ['status' => 'bidding']);
        TenderParticipant::create(['tender_id' => $tender->id, 'vendor_id' => $vendor['vendor']->id, 'joined_at' => now()]);
        $item = TenderItem::create(['tender_id' => $tender->id, 'description' => 'Item 1', 'unit' => 'pcs', 'quantity' => 1, 'hps_unit_price' => 10000]);

        $this->actingAs($vendor['user'], 'api')->postJson("/api/tenders/{$tender->id}/penawaran", [
            'bid_amount' => 10000,
            'items' => [
                ['tender_item_id' => $item->id, 'unit_price' => 5000],
                ['tender_item_id' => $item->id, 'unit_price' => 5000],
            ],
        ])->assertUnprocessable();
    }

    public function test_bid_submission_rejects_item_from_another_tender(): void
    {
        $vendor = $this->vendor();
        $admin = User::factory()->create(['role' => 'admin']);
        $tenderA = $this->createTender($admin, ['status' => 'bidding']);
        $tenderB = $this->createTender($admin, ['status' => 'bidding']);
        TenderParticipant::create(['tender_id' => $tenderA->id, 'vendor_id' => $vendor['vendor']->id, 'joined_at' => now()]);
        $otherItem = TenderItem::create(['tender_id' => $tenderB->id, 'description' => 'Other Item', 'unit' => 'pcs', 'quantity' => 1, 'hps_unit_price' => 10000]);

        $this->actingAs($vendor['user'], 'api')->postJson("/api/tenders/{$tenderA->id}/penawaran", [
            'bid_amount' => 10000,
            'items' => [
                ['tender_item_id' => $otherItem->id, 'unit_price' => 10000],
            ],
        ])->assertUnprocessable();
    }

    private function createTender(User $admin, array $overrides = []): Tender
    {
        return Tender::create(array_merge([
            'created_by' => $admin->id,
            'title' => 'Tender '.uniqid(),
            'category' => 'konstruksi',
            'description' => 'Description',
            'specification' => 'Specification',
            'hps' => 10000000,
            'start_date' => now()->subDay(),
            'end_date' => now()->addMonth(),
            'bidding_start' => now()->subDay(),
            'bidding_end' => now()->addWeek(),
            'status' => 'open',
        ], $overrides));
    }

    private function vendor(): array
    {
        $user = User::factory()->create(['role' => 'vendor']);
        $vendor = Vendor::create([
            'user_id' => $user->id,
            'company_name' => fake()->company(),
            'phone' => '081234567890',
            'address' => fake()->address(),
            'verification_status' => 'approved',
        ]);

        return compact('user', 'vendor');
    }
}
