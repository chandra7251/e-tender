<?php

namespace Database\Seeders;

use App\Models\Bid;
use App\Models\BidHistory;
use App\Models\PurchaseOrder;
use App\Models\Tender;
use App\Models\TenderAnnouncement;
use App\Models\TenderHistory;
use App\Models\TenderParticipant;
use App\Models\TenderResult;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorDocument;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // ─── 1. Admin ─────────────────────────────────────────────────────────
        $admin = User::create([
            'name'     => 'Admin Procurement',
            'email'    => 'admin@example.com',
            'password' => Hash::make('password'),
            'role'     => 'admin',
        ]);

        // ─── 2. Vendor Pending ────────────────────────────────────────────────
        $userPending = User::create([
            'name'     => 'Vendor Pending',
            'email'    => 'vendor.pending@example.com',
            'password' => Hash::make('password'),
            'role'     => 'vendor',
        ]);

        Vendor::create([
            'user_id'             => $userPending->id,
            'company_name'        => 'PT Pending Jaya',
            'phone'               => '081200000001',
            'address'             => 'Jl. Pending No. 1, Jakarta',
            'verification_status' => 'pending',
        ]);

        // ─── 3. Vendor Approved ───────────────────────────────────────────────
        $userApproved = User::create([
            'name'     => 'Vendor Approved',
            'email'    => 'vendor.approved@example.com',
            'password' => Hash::make('password'),
            'role'     => 'vendor',
        ]);

        $vendorApproved = Vendor::create([
            'user_id'             => $userApproved->id,
            'company_name'        => 'PT Approved Maju',
            'phone'               => '081200000002',
            'address'             => 'Jl. Maju No. 2, Bandung',
            'verification_status' => 'approved',
            'verified_by'         => $admin->id,
            'verified_at'         => now()->subDays(5),
        ]);

        // ─── 4. Dokumen Vendor Approved ───────────────────────────────────────
        VendorDocument::create([
            'vendor_id'     => $vendorApproved->id,
            'document_type' => 'legalitas',
            'file_name'     => 'akta_pendirian.pdf',
            'file_path'     => 'documents/vendors/' . $vendorApproved->id . '/akta_pendirian.pdf',
            'mime_type'     => 'application/pdf',
            'file_size'     => 204800,
            'uploaded_at'   => now()->subDays(6),
        ]);

        VendorDocument::create([
            'vendor_id'     => $vendorApproved->id,
            'document_type' => 'izin_usaha',
            'file_name'     => 'siup.pdf',
            'file_path'     => 'documents/vendors/' . $vendorApproved->id . '/siup.pdf',
            'mime_type'     => 'application/pdf',
            'file_size'     => 153600,
            'uploaded_at'   => now()->subDays(6),
        ]);

        // ─── 4b. Vendor Approved Kedua (untuk demo competition bidding) ───────
        $userApproved2 = User::create([
            'name'     => 'Vendor Kedua',
            'email'    => 'vendor.kedua@example.com',
            'password' => Hash::make('password'),
            'role'     => 'vendor',
        ]);

        $vendorApproved2 = Vendor::create([
            'user_id'             => $userApproved2->id,
            'company_name'        => 'CV Karya Prima',
            'phone'               => '081200000003',
            'address'             => 'Jl. Prima No. 3, Surabaya',
            'verification_status' => 'approved',
            'verified_by'         => $admin->id,
            'verified_at'         => now()->subDays(4),
        ]);

        // ─── 5. Tender Draft ──────────────────────────────────────────────────
        $tenderDraft = Tender::create([
            'created_by'    => $admin->id,
            'title'         => 'Pengadaan Alat Tulis Kantor 2026',
            'description'   => 'Pengadaan alat tulis kantor untuk kebutuhan operasional tahun 2026.',
            'specification' => 'Spesifikasi ATK meliputi kertas A4, pulpen, map, dan perlengkapan kantor lainnya.',
            'start_date'    => now()->addDays(30),
            'end_date'      => now()->addDays(60),
            'aanwijzing_date' => null,
            'bidding_start' => now()->addDays(35),
            'bidding_end'   => now()->addDays(50),
            'status'        => 'draft',
        ]);

        TenderHistory::create([
            'tender_id'  => $tenderDraft->id,
            'actor_id'   => $admin->id,
            'action'     => 'tender_created',
            'new_status' => 'draft',
            'description' => 'Tender dibuat oleh admin.',
            'created_at' => now(),
        ]);

        // ─── 5b. Tender Open (untuk demo API vendor join/bid) ─────────────────
        $tenderOpen = Tender::create([
            'created_by'      => $admin->id,
            'title'           => 'Pengadaan Furnitur Kantor 2026',
            'description'     => 'Pengadaan meja, kursi, dan lemari arsip untuk kantor baru.',
            'specification'   => 'Minimal 20 meja kerja, 40 kursi ergonomis, 10 lemari arsip besi.',
            'start_date'      => now()->subDay(),
            'end_date'        => now()->addDays(25),
            'aanwijzing_date' => now()->addDays(3),
            'bidding_start'   => now()->addDays(5),
            'bidding_end'     => now()->addDays(15),
            'status'          => 'open',
        ]);

        TenderHistory::create([
            'tender_id'   => $tenderOpen->id,
            'actor_id'    => $admin->id,
            'action'      => 'tender_created',
            'new_status'  => 'draft',
            'description' => 'Tender dibuat oleh admin.',
            'created_at'  => now()->subDay(),
        ]);

        TenderHistory::create([
            'tender_id'   => $tenderOpen->id,
            'actor_id'    => $admin->id,
            'action'      => 'status_changed',
            'old_status'  => 'draft',
            'new_status'  => 'open',
            'description' => 'Tender dibuka untuk pendaftaran vendor.',
            'created_at'  => now()->subHours(2),
        ]);

        // ─── 6. Tender Aktif (Bidding) ────────────────────────────────────────
        $tenderActive = Tender::create([
            'created_by'      => $admin->id,
            'title'           => 'Pengadaan Komputer dan Perangkat IT 2026',
            'description'     => 'Pengadaan komputer, laptop, dan perangkat IT untuk kebutuhan kantor pusat.',
            'specification'   => 'Spesifikasi: Laptop Core i5 Gen 12, RAM 16GB, SSD 512GB, minimal 10 unit.',
            'start_date'      => now()->subDays(10),
            'end_date'        => now()->addDays(20),
            'aanwijzing_date' => now()->subDays(5),
            'bidding_start'   => now()->subDays(3),
            'bidding_end'     => now()->addDays(7),
            'status'          => 'bidding',
        ]);

        TenderHistory::create([
            'tender_id'   => $tenderActive->id,
            'actor_id'    => $admin->id,
            'action'      => 'tender_created',
            'new_status'  => 'draft',
            'description' => 'Tender dibuat oleh admin.',
            'created_at'  => now()->subDays(10),
        ]);

        TenderHistory::create([
            'tender_id'   => $tenderActive->id,
            'actor_id'    => $admin->id,
            'action'      => 'status_changed',
            'old_status'  => 'draft',
            'new_status'  => 'bidding',
            'description' => 'Status tender diubah ke bidding.',
            'created_at'  => now()->subDays(3),
        ]);

        // ─── 7. Aanwijzing / Announcement ────────────────────────────────────
        TenderAnnouncement::create([
            'tender_id'    => $tenderActive->id,
            'created_by'   => $admin->id,
            'title'        => 'Aanwijzing Pengadaan Komputer 2026',
            'content'      => 'Rapat penjelasan dokumen tender akan dilaksanakan secara online melalui Zoom. Semua vendor terdaftar wajib hadir.',
            'published_at' => now()->subDays(5),
        ]);

        // ─── 8. Participant ────────────────────────────────────────────────────
        $participant = TenderParticipant::create([
            'tender_id' => $tenderActive->id,
            'vendor_id' => $vendorApproved->id,
            'joined_at' => now()->subDays(8),
        ]);

        TenderHistory::create([
            'tender_id'   => $tenderActive->id,
            'actor_id'    => $userApproved->id,
            'action'      => 'vendor_joined',
            'description' => 'Vendor ' . $vendorApproved->company_name . ' bergabung ke tender.',
            'created_at'  => $participant->joined_at,
        ]);

        // ─── 9. Bid dari Vendor Approved ──────────────────────────────────────
        $bid = Bid::create([
            'tender_id'    => $tenderActive->id,
            'vendor_id'    => $vendorApproved->id,
            'bid_amount'   => 95000000.00,
            'notes'        => 'Penawaran termasuk garansi 1 tahun dan instalasi.',
            'submitted_at' => now()->subDays(2),
        ]);

        // ─── 10. Bid History (initial submission) ─────────────────────────────
        BidHistory::create([
            'bid_id'         => $bid->id,
            'tender_id'      => $tenderActive->id,
            'vendor_id'      => $vendorApproved->id,
            'old_bid_amount' => null,
            'new_bid_amount' => 95000000.00,
            'notes'          => 'Penawaran pertama diajukan.',
            'changed_at'     => now()->subDays(2),
            'created_at'     => now()->subDays(2),
        ]);

        // Simulasi revisi bid
        $bid->update(['bid_amount' => 92500000.00, 'submitted_at' => now()->subDay()]);

        BidHistory::create([
            'bid_id'         => $bid->id,
            'tender_id'      => $tenderActive->id,
            'vendor_id'      => $vendorApproved->id,
            'old_bid_amount' => 95000000.00,
            'new_bid_amount' => 92500000.00,
            'notes'          => 'Revisi penawaran — harga diturunkan.',
            'changed_at'     => now()->subDay(),
            'created_at'     => now()->subDay(),
        ]);

        TenderHistory::create([
            'tender_id'   => $tenderActive->id,
            'actor_id'    => $userApproved->id,
            'action'      => 'bid_submitted',
            'description' => 'Vendor mengajukan penawaran senilai Rp 92.500.000.',
            'metadata'    => ['bid_id' => $bid->id, 'bid_amount' => 92500000.00],
            'created_at'  => now()->subDay(),
        ]);

        // ─── 11. Tender Result & PO (1 contoh konsisten) ──────────────────────
        $result = TenderResult::create([
            'tender_id'          => $tenderActive->id,
            'winner_vendor_id'   => $vendorApproved->id,
            'winning_bid_id'     => $bid->id,
            'winning_bid_amount' => 92500000.00,
            'selection_method'   => 'lowest_price',
            'notes'              => 'Vendor terpilih karena menawarkan harga terendah dengan spesifikasi sesuai.',
            'decided_by'         => $admin->id,
            'decided_at'         => now(),
        ]);

        TenderHistory::create([
            'tender_id'   => $tenderActive->id,
            'actor_id'    => $admin->id,
            'action'      => 'winner_selected',
            'old_status'  => 'bidding',
            'new_status'  => 'finished',
            'description' => 'Pemenang tender dipilih: ' . $vendorApproved->company_name,
            'metadata'    => ['winner_vendor_id' => $vendorApproved->id, 'winning_bid_amount' => 92500000.00],
            'created_at'  => now(),
        ]);

        $po = PurchaseOrder::create([
            'tender_result_id' => $result->id,
            'tender_id'        => $tenderActive->id,
            'vendor_id'        => $vendorApproved->id,
            'po_number'        => 'PO-' . date('Ymd') . '-001',
            'amount'           => 92500000.00,
            'issued_date'      => today(),
            'notes'            => 'Purchase Order untuk pengadaan komputer dan perangkat IT.',
            'generated_by'     => $admin->id,
        ]);

        TenderHistory::create([
            'tender_id'   => $tenderActive->id,
            'actor_id'    => $admin->id,
            'action'      => 'po_generated',
            'description' => 'Purchase Order ' . $po->po_number . ' diterbitkan.',
            'metadata'    => ['po_id' => $po->id, 'po_number' => $po->po_number],
            'created_at'  => now(),
        ]);

        // Update status tender ke finished
        $tenderActive->update(['status' => 'finished']);
    }
}
