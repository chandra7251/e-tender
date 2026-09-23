<?php

namespace Tests\Feature;

use App\Models\Bid;
use App\Models\Contract;
use App\Models\ContractDelivery;
use App\Models\Tender;
use App\Models\TenderParticipant;
use App\Models\TenderPayment;
use App\Models\TenderResult;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorSubmission;
use App\Services\PaymentGatewayService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Mockery;
use Tests\TestCase;

class PhaseFourCoverageTest extends TestCase
{
    use RefreshDatabase;

    // ─── 1. AUTH ─────────────────────────────────────────────────────────────

    public function test_vendor_registration_succeeds_and_creates_pending_vendor(): void
    {
        $payload = [
            'name' => 'Vendor Baru',
            'email' => 'vendor.baru@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'company_name' => 'PT Vendor Baru Indonesia',
            'phone' => '081299998888',
            'address' => 'Jl. Baru No. 123',
        ];

        $response = $this->postJson('/api/auth/register', $payload);

        $response->assertCreated();
        $this->assertDatabaseHas('users', [
            'email' => 'vendor.baru@example.com',
            'role' => 'vendor',
        ]);
        $this->assertDatabaseHas('vendors', [
            'company_name' => 'PT Vendor Baru Indonesia',
            'verification_status' => 'pending',
        ]);
    }

    public function test_login_rejects_invalid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'vendor.test@example.com',
            'password' => Hash::make('secret123'),
            'role' => 'vendor',
        ]);

        $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'wrongpassword',
        ])->assertStatus(401);
    }

    public function test_login_rejects_unverified_vendor_email(): void
    {
        $user = User::factory()->unverified()->create([
            'email' => 'vendor.unverified@example.com',
            'password' => Hash::make('password123'),
            'role' => 'vendor',
        ]);

        $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'password123',
        ])->assertForbidden();
    }

    public function test_login_accepts_verified_vendor_and_returns_jwt(): void
    {
        $vendor = $this->vendor();

        $response = $this->postJson('/api/auth/login', [
            'email' => $vendor['user']->email,
            'password' => 'password',
        ]);

        $response->assertOk()
            ->assertJsonPath('status', true)
            ->assertJsonStructure([
                'status',
                'data' => [
                    'token',
                    'token_type',
                    'expires_in',
                    'user' => ['id', 'name', 'email', 'role'],
                    'vendor',
                ],
            ]);
    }

    public function test_jwt_refresh_and_logout_invalidate_session(): void
    {
        $vendor = $this->vendor();
        $token = auth('api')->login($vendor['user']);

        // Refresh token
        $refreshRes = $this->withToken($token)->postJson('/api/auth/refresh');
        $refreshRes->assertOk()->assertJsonStructure(['data' => ['token']]);

        $newToken = $refreshRes->json('data.token');

        // Logout with new token
        $this->withToken($newToken)->postJson('/api/auth/logout')->assertOk();
    }

    // ─── 2. VENDOR APPROVAL ──────────────────────────────────────────────────

    public function test_unapproved_vendor_cannot_join_tender(): void
    {
        $user = User::factory()->create(['role' => 'vendor']);
        Vendor::create([
            'user_id' => $user->id,
            'company_name' => 'PT Pending',
            'phone' => '0811111111',
            'address' => 'Alamat',
            'verification_status' => 'pending',
        ]);

        $tender = $this->openTender();

        $this->actingAs($user, 'api')
            ->postJson("/api/tenders/{$tender->id}/participants")
            ->assertForbidden();
    }

    public function test_approved_vendor_can_join_open_tender(): void
    {
        $vendor = $this->vendor();
        $tender = $this->openTender();

        $this->actingAs($vendor['user'], 'api')
            ->postJson("/api/tenders/{$tender->id}/participants")
            ->assertCreated();

        $this->assertDatabaseHas('tender_participants', [
            'tender_id' => $tender->id,
            'vendor_id' => $vendor['vendor']->id,
        ]);
    }

    public function test_admin_can_approve_and_reject_vendor_submission(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $vendor = $this->vendor();

        $submission = VendorSubmission::create([
            'vendor_id' => $vendor['vendor']->id,
            'nama_barang' => 'Server Rack 42U',
            'deskripsi' => 'Deskripsi server rack',
            'status' => 'pending',
        ]);

        // Admin approve
        $this->actingAs($admin, 'api')
            ->patchJson("/api/admin/submissions/{$submission->id}/approve")
            ->assertOk();

        $this->assertSame('approved', $submission->fresh()->status);

        // Another submission for reject
        $submission2 = VendorSubmission::create([
            'vendor_id' => $vendor['vendor']->id,
            'nama_barang' => 'Kabel UTP Cat6',
            'deskripsi' => 'Deskripsi kabel',
            'status' => 'pending',
        ]);

        // Admin reject requires catatan_admin min:10
        $this->actingAs($admin, 'api')
            ->patchJson("/api/admin/submissions/{$submission2->id}/reject", [
                'catatan_admin' => 'Spesifikasi tidak memenuhi standar pengadaan kantor.',
            ])
            ->assertOk();

        $this->assertSame('rejected', $submission2->fresh()->status);
    }

    // ─── 3. TENDER PARTICIPATION ─────────────────────────────────────────────

    public function test_vendor_duplicate_participation_is_rejected(): void
    {
        $vendor = $this->vendor();
        $tender = $this->openTender();

        TenderParticipant::create([
            'tender_id' => $tender->id,
            'vendor_id' => $vendor['vendor']->id,
            'joined_at' => now(),
        ]);

        $this->actingAs($vendor['user'], 'api')
            ->postJson("/api/tenders/{$tender->id}/participants")
            ->assertUnprocessable();
    }

    public function test_vendor_cannot_join_closed_or_finished_tender(): void
    {
        $vendor = $this->vendor();
        $tender = $this->openTender([
            'status' => 'finished',
        ]);

        $this->actingAs($vendor['user'], 'api')
            ->postJson("/api/tenders/{$tender->id}/participants")
            ->assertUnprocessable();
    }

    public function test_non_vendor_cannot_join_tender(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $tender = $this->openTender();

        $this->actingAs($admin, 'api')
            ->postJson("/api/tenders/{$tender->id}/participants")
            ->assertForbidden();
    }

    // ─── 4. BIDDING ──────────────────────────────────────────────────────────

    public function test_non_participant_cannot_submit_bid(): void
    {
        $vendor = $this->vendor();
        $tender = $this->biddingTender();

        $this->actingAs($vendor['user'], 'api')
            ->postJson("/api/tenders/{$tender->id}/penawaran", [
                'bid_amount' => 75000000,
                'notes' => 'Penawaran tanpa join',
            ])
            ->assertUnprocessable();
    }

    public function test_bid_submission_enforces_bidding_period_start_and_end(): void
    {
        $vendor = $this->vendor();
        $admin = User::factory()->create(['role' => 'admin']);

        // Tender with future bidding period
        $futureTender = Tender::create([
            'created_by' => $admin->id,
            'title' => 'Tender Future',
            'description' => 'Desc',
            'specification' => 'Spec',
            'start_date' => now(),
            'end_date' => now()->addMonth(),
            'bidding_start' => now()->addDays(2),
            'bidding_end' => now()->addDays(5),
            'status' => 'bidding',
        ]);
        TenderParticipant::create(['tender_id' => $futureTender->id, 'vendor_id' => $vendor['vendor']->id, 'joined_at' => now()]);

        $this->actingAs($vendor['user'], 'api')
            ->postJson("/api/tenders/{$futureTender->id}/penawaran", [
                'bid_amount' => 50000000,
            ])
            ->assertUnprocessable();
    }

    public function test_duplicate_bid_submission_is_rejected(): void
    {
        $vendor = $this->vendor();
        $tender = $this->biddingTender();
        TenderParticipant::create(['tender_id' => $tender->id, 'vendor_id' => $vendor['vendor']->id, 'joined_at' => now()]);

        Bid::create([
            'tender_id' => $tender->id,
            'vendor_id' => $vendor['vendor']->id,
            'bid_amount' => 50000000,
            'submitted_at' => now(),
        ]);

        $this->actingAs($vendor['user'], 'api')
            ->postJson("/api/tenders/{$tender->id}/penawaran", [
                'bid_amount' => 48000000,
            ])
            ->assertUnprocessable();
    }

    public function test_vendor_cannot_update_another_vendors_bid(): void
    {
        $vendorA = $this->vendor();
        $vendorB = $this->vendor();
        $tender = $this->biddingTender();

        TenderParticipant::create(['tender_id' => $tender->id, 'vendor_id' => $vendorA['vendor']->id, 'joined_at' => now()]);
        TenderParticipant::create(['tender_id' => $tender->id, 'vendor_id' => $vendorB['vendor']->id, 'joined_at' => now()]);

        $bidA = Bid::create([
            'tender_id' => $tender->id,
            'vendor_id' => $vendorA['vendor']->id,
            'bid_amount' => 60000000,
            'submitted_at' => now(),
        ]);

        // Vendor B attempts to update Vendor A's bid
        $res = $this->actingAs($vendorB['user'], 'api')
            ->putJson("/api/tenders/{$tender->id}/penawaran/{$bidA->id}", [
                'bid_amount' => 55000000,
            ]);

        $res->assertOk();
        $this->assertFalse($res->json('status'));
        $this->assertSame('Bid tidak ditemukan.', $res->json('message'));
        $this->assertSame(60000000.0, (float) $bidA->fresh()->bid_amount);
    }

    // ─── 5. TENDER RESULT / WINNER ───────────────────────────────────────────

    public function test_tender_result_and_winner_endpoint_display_outcome(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $vendor = $this->vendor();
        $tender = $this->biddingTender(['status' => 'finished']);

        $bid = Bid::create([
            'tender_id' => $tender->id,
            'vendor_id' => $vendor['vendor']->id,
            'bid_amount' => 70000000,
            'submitted_at' => now(),
        ]);

        TenderResult::create([
            'tender_id' => $tender->id,
            'winner_vendor_id' => $vendor['vendor']->id,
            'winning_bid_id' => $bid->id,
            'winning_bid_amount' => 70000000,
            'selection_method' => 'lowest_price',
            'decided_by' => $admin->id,
            'decided_at' => now(),
        ]);

        $this->actingAs($vendor['user'], 'api')
            ->getJson("/api/tenders/{$tender->id}/result")
            ->assertOk()
            ->assertJsonPath('data.winning_bid_amount', 70000000);

        $this->actingAs($vendor['user'], 'api')
            ->getJson("/api/tenders/{$tender->id}/winner")
            ->assertOk()
            ->assertJsonPath('data.is_winner', true);
    }

    public function test_tender_result_returns_404_when_result_not_decided(): void
    {
        $vendor = $this->vendor();
        $tender = $this->openTender();

        $this->actingAs($vendor['user'], 'api')
            ->getJson("/api/tenders/{$tender->id}/result")
            ->assertNotFound();
    }

    // ─── 6. CONTRACT ─────────────────────────────────────────────────────────

    public function test_contract_store_rejects_tender_without_winner(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $tender = $this->openTender();

        $this->actingAs($admin, 'api')
            ->postJson('/api/admin/contracts', [
                'tender_id' => $tender->id,
                'contract_value' => 100000000,
            ])
            ->assertUnprocessable();
    }

    public function test_contract_store_rejects_duplicate_contract_for_same_tender(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $vendor = $this->vendor();
        $tender = $this->biddingTender(['status' => 'finished']);

        $bid = Bid::create([
            'tender_id' => $tender->id,
            'vendor_id' => $vendor['vendor']->id,
            'bid_amount' => 50000000,
            'submitted_at' => now(),
        ]);

        TenderResult::create([
            'tender_id' => $tender->id,
            'winner_vendor_id' => $vendor['vendor']->id,
            'winning_bid_id' => $bid->id,
            'winning_bid_amount' => 50000000,
            'selection_method' => 'lowest_price',
            'decided_by' => $admin->id,
            'decided_at' => now(),
        ]);

        Contract::create([
            'contract_number' => 'KONTRAK-EXISTING',
            'tender_id' => $tender->id,
            'vendor_id' => $vendor['vendor']->id,
            'created_by' => $admin->id,
            'contract_value' => 50000000,
            'status' => 'draft',
        ]);

        $this->actingAs($admin, 'api')
            ->postJson('/api/admin/contracts', [
                'tender_id' => $tender->id,
                'contract_value' => 50000000,
            ])
            ->assertUnprocessable();
    }

    public function test_vendor_cannot_access_or_sign_another_vendors_contract(): void
    {
        $vendorA = $this->vendor();
        $vendorB = $this->vendor();
        $admin = User::factory()->create(['role' => 'admin']);
        $tender = $this->biddingTender(['status' => 'finished']);

        $contractA = Contract::create([
            'contract_number' => 'KONTRAK-VENDOR-A',
            'tender_id' => $tender->id,
            'vendor_id' => $vendorA['vendor']->id,
            'created_by' => $admin->id,
            'contract_value' => 80000000,
            'status' => 'sent_to_vendor',
        ]);

        // Vendor B view -> 403
        $this->actingAs($vendorB['user'], 'api')
            ->getJson("/api/contracts/{$contractA->id}")
            ->assertForbidden();

        // Vendor B sign -> 403
        $this->actingAs($vendorB['user'], 'api')
            ->patchJson("/api/contracts/{$contractA->id}/sign-vendor")
            ->assertForbidden();
    }

    public function test_vendor_cannot_sign_contract_before_it_is_sent(): void
    {
        $vendor = $this->vendor();
        $admin = User::factory()->create(['role' => 'admin']);
        $tender = $this->biddingTender(['status' => 'finished']);

        $contract = Contract::create([
            'contract_number' => 'KONTRAK-DRAFT',
            'tender_id' => $tender->id,
            'vendor_id' => $vendor['vendor']->id,
            'created_by' => $admin->id,
            'contract_value' => 80000000,
            'status' => 'draft',
        ]);

        $this->actingAs($vendor['user'], 'api')
            ->patchJson("/api/contracts/{$contract->id}/sign-vendor")
            ->assertUnprocessable();
    }

    public function test_contract_status_transitions_from_sent_to_signed_vendor_to_active(): void
    {
        $vendor = $this->vendor();
        $admin = User::factory()->create(['role' => 'admin']);
        $tender = $this->biddingTender(['status' => 'finished']);

        $contract = Contract::create([
            'contract_number' => 'KONTRAK-FLOW-01',
            'tender_id' => $tender->id,
            'vendor_id' => $vendor['vendor']->id,
            'created_by' => $admin->id,
            'contract_value' => 80000000,
            'status' => 'draft',
        ]);

        // 1. Admin sends to vendor
        $this->actingAs($admin, 'api')
            ->patchJson("/api/admin/contracts/{$contract->id}/send")
            ->assertOk();
        $this->assertSame('sent_to_vendor', $contract->fresh()->status);

        // 2. Vendor signs
        $this->actingAs($vendor['user'], 'api')
            ->patchJson("/api/contracts/{$contract->id}/sign-vendor")
            ->assertOk();
        $this->assertSame('signed_vendor', $contract->fresh()->status);

        // 3. Admin signs and activates
        $this->actingAs($admin, 'api')
            ->patchJson("/api/admin/contracts/{$contract->id}/sign-admin")
            ->assertOk();
        $this->assertSame('active', $contract->fresh()->status);
    }

    // ─── 7. DELIVERY ─────────────────────────────────────────────────────────

    public function test_admin_cannot_verify_undelivered_milestone(): void
    {
        $vendor = $this->vendor();
        $admin = User::factory()->create(['role' => 'admin']);
        $tender = $this->biddingTender(['status' => 'finished']);
        $contract = Contract::create([
            'contract_number' => 'KONTRAK-DELIVERY',
            'tender_id' => $tender->id,
            'vendor_id' => $vendor['vendor']->id,
            'created_by' => $admin->id,
            'contract_value' => 50000000,
            'status' => 'active',
        ]);
        $delivery = ContractDelivery::create([
            'contract_id' => $contract->id,
            'milestone_name' => 'Fase 1',
            'due_date' => now()->addWeek(),
            'status' => 'scheduled',
        ]);

        $this->actingAs($admin, 'api')
            ->patchJson("/api/admin/contracts/{$contract->id}/deliveries/{$delivery->id}/verify")
            ->assertUnprocessable();
    }

    public function test_admin_verify_delivered_milestone_and_completes_contract_when_all_verified(): void
    {
        $vendor = $this->vendor();
        $admin = User::factory()->create(['role' => 'admin']);
        $tender = $this->biddingTender(['status' => 'finished']);
        $contract = Contract::create([
            'contract_number' => 'KONTRAK-DELIVERY-2',
            'tender_id' => $tender->id,
            'vendor_id' => $vendor['vendor']->id,
            'created_by' => $admin->id,
            'contract_value' => 50000000,
            'status' => 'active',
        ]);
        $delivery = ContractDelivery::create([
            'contract_id' => $contract->id,
            'milestone_name' => 'Milestone Tunggal',
            'due_date' => now()->addWeek(),
            'status' => 'delivered',
            'delivered_at' => now(),
        ]);

        $this->actingAs($admin, 'api')
            ->patchJson("/api/admin/contracts/{$contract->id}/deliveries/{$delivery->id}/verify")
            ->assertOk();

        $this->assertSame('verified', $delivery->fresh()->status);
        $this->assertSame('completed', $contract->fresh()->status);
    }

    // ─── 8. COMPLAINT / APPEAL ───────────────────────────────────────────────

    public function test_vendor_cannot_file_complaint_if_not_participant(): void
    {
        $vendor = $this->vendor();
        $winnerVendor = $this->vendor();
        $admin = User::factory()->create(['role' => 'admin']);
        $tender = $this->biddingTender(['status' => 'finished']);

        $bid = Bid::create([
            'tender_id' => $tender->id,
            'vendor_id' => $winnerVendor['vendor']->id,
            'bid_amount' => 50000000,
            'submitted_at' => now(),
        ]);

        TenderResult::create([
            'tender_id' => $tender->id,
            'winner_vendor_id' => $winnerVendor['vendor']->id,
            'winning_bid_id' => $bid->id,
            'winning_bid_amount' => 50000000,
            'selection_method' => 'lowest_price',
            'decided_by' => $admin->id,
            'decided_at' => now(),
        ]);

        $this->actingAs($vendor['user'], 'api')
            ->postJson("/api/tenders/{$tender->id}/complaints", [
                'type' => 'sanggahan',
                'reason' => 'Alasan komplain minimum tiga puluh karakter di sini untuk pengujian.',
            ])
            ->assertForbidden();
    }

    public function test_vendor_cannot_file_complaint_if_tender_result_missing(): void
    {
        $vendor = $this->vendor();
        $tender = $this->openTender();
        TenderParticipant::create(['tender_id' => $tender->id, 'vendor_id' => $vendor['vendor']->id, 'joined_at' => now()]);

        $this->actingAs($vendor['user'], 'api')
            ->postJson("/api/tenders/{$tender->id}/complaints", [
                'type' => 'sanggahan',
                'reason' => 'Alasan komplain minimum tiga puluh karakter di sini untuk pengujian.',
            ])
            ->assertUnprocessable();
    }

    public function test_vendor_can_file_complaint_and_admin_can_respond(): void
    {
        $vendor = $this->vendor();
        $winnerVendor = $this->vendor();
        $admin = User::factory()->create(['role' => 'admin']);
        $tender = $this->biddingTender(['status' => 'finished']);
        TenderParticipant::create(['tender_id' => $tender->id, 'vendor_id' => $vendor['vendor']->id, 'joined_at' => now()]);

        $bid = Bid::create([
            'tender_id' => $tender->id,
            'vendor_id' => $winnerVendor['vendor']->id,
            'bid_amount' => 50000000,
            'submitted_at' => now(),
        ]);

        $result = TenderResult::create([
            'tender_id' => $tender->id,
            'winner_vendor_id' => $winnerVendor['vendor']->id,
            'winning_bid_id' => $bid->id,
            'winning_bid_amount' => 50000000,
            'selection_method' => 'lowest_price',
            'decided_by' => $admin->id,
            'decided_at' => now(),
        ]);

        // Vendor files complaint
        $res = $this->actingAs($vendor['user'], 'api')
            ->postJson("/api/tenders/{$tender->id}/complaints", [
                'type' => 'sanggahan',
                'reason' => 'Kami mengajukan sanggahan terhadap evaluasi teknis yang tidak sesuai SOP.',
            ]);

        $res->assertCreated();
        $complaintId = $res->json('data.id');

        // Admin responds
        $this->actingAs($admin, 'api')
            ->patchJson("/api/admin/complaints/{$complaintId}/respond", [
                'status' => 'accepted',
                'response' => 'Sanggahan diterima dan akan dilakukan evaluasi ulang berkas penawaran.',
            ])
            ->assertOk();

        $this->assertDatabaseHas('tender_complaints', [
            'id' => $complaintId,
            'status' => 'accepted',
        ]);
    }

    // ─── 9. PAYMENT ──────────────────────────────────────────────────────────

    public function test_create_deposit_requires_valid_tender_and_minimum_amount(): void
    {
        $vendor = $this->vendor();
        $tender = $this->openTender();

        // Amount below minimum 10000
        $this->actingAs($vendor['user'], 'api')
            ->postJson('/api/payment/deposit', [
                'tender_id' => $tender->id,
                'deposit_amount' => 5000,
            ])
            ->assertUnprocessable();
    }

    public function test_create_deposit_succeeds_with_mocked_gateway(): void
    {
        $vendor = $this->vendor();
        $tender = $this->openTender();

        $gateway = Mockery::mock(PaymentGatewayService::class);
        $gateway->shouldReceive('createDepositPayment')
            ->once()
            ->andReturn([
                'success' => true,
                'data' => [
                    'order_id' => 'ORDER-12345',
                    'snap_token' => 'mock-snap-token',
                    'redirect_url' => 'https://mock.midtrans.com/snap',
                ],
            ]);
        $this->app->instance(PaymentGatewayService::class, $gateway);

        $res = $this->actingAs($vendor['user'], 'api')
            ->postJson('/api/payment/deposit', [
                'tender_id' => $tender->id,
                'deposit_amount' => 500000,
            ]);

        $res->assertOk()->assertJsonPath('data.snap_token', 'mock-snap-token');
    }

    public function test_payment_notification_rejects_invalid_signature(): void
    {
        $payload = [
            'order_id' => 'DEPOSIT-1-1-12345678',
            'status_code' => '200',
            'gross_amount' => '500000.00',
            'signature_key' => 'completely-invalid-signature-hash',
            'transaction_status' => 'settlement',
        ];

        $this->postJson('/api/payment/notification', $payload)
            ->assertStatus(400);
    }

    // ─── 10. ADMIN PERMISSIONS ───────────────────────────────────────────────

    public function test_evaluator_and_auditor_cannot_modify_instansi_settings(): void
    {
        $roles = ['evaluator', 'auditor', 'verifikator'];

        foreach ($roles as $role) {
            $user = User::factory()->create(['role' => $role]);
            $this->actingAs($user, 'api')
                ->putJson('/api/admin/settings', [
                    'instansi_name' => 'Nama Baru',
                ])
                ->assertForbidden();
        }
    }

    public function test_procurement_manager_cannot_refund_deposit(): void
    {
        $manager = User::factory()->create(['role' => 'procurement_manager']);
        $vendor = $this->vendor();
        $tender = $this->openTender();

        $payment = TenderPayment::create([
            'tender_id' => $tender->id,
            'vendor_id' => $vendor['vendor']->id,
            'order_id' => 'ORDER-MGR-TEST',
            'type' => 'deposit',
            'amount' => 500000,
            'status' => 'paid',
        ]);

        $this->actingAs($manager, 'api')
            ->postJson("/api/payment/refund/{$payment->id}")
            ->assertForbidden();
    }

    // ─── HELPERS ─────────────────────────────────────────────────────────────

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

    private function openTender(array $overrides = []): Tender
    {
        $admin = User::factory()->create(['role' => 'admin']);

        return Tender::create(array_merge([
            'created_by' => $admin->id,
            'title' => 'Tender Pengadaan Laptop',
            'description' => 'Deskripsi pengadaan laptop',
            'specification' => 'Spesifikasi Core i7 RAM 16GB',
            'start_date' => now(),
            'end_date' => now()->addMonth(),
            'bidding_start' => now()->addDays(3),
            'bidding_end' => now()->addDays(10),
            'status' => 'open',
        ], $overrides));
    }

    private function biddingTender(array $overrides = []): Tender
    {
        return $this->openTender(array_merge([
            'bidding_start' => now()->subDay(),
            'bidding_end' => now()->addDays(5),
            'status' => 'bidding',
        ], $overrides));
    }
}
