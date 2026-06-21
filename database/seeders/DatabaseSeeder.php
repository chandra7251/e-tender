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

/**
 * DatabaseSeeder — E-Procurement Demo Data
 *
 * Skenario demo yang tersedia:
 *
 * [A] TENDER BIDDING (Pengadaan Laptop dan Aksesori 2026)
 *     Status  : bidding
 *     Timeline: bidding_start = 3 hari lalu, bidding_end = 7 hari ke depan
 *     Peserta : 2 vendor approved (keduanya sudah join, belum bid)
 *     Winner  : BELUM ADA → untuk demo proses submit bid & pilih pemenang
 *
 * [B] TENDER FINISHED (Pengadaan Komputer dan Perangkat IT 2026)
 *     Status  : finished
 *     Peserta : 2 vendor approved (keduanya join + punya bid)
 *     Winner  : PT Approved Maju (bid Rp 92.500.000 — lowest)
 *     PO      : sudah ada → untuk demo halaman result, winner, dan PO
 *
 * [C] TENDER OPEN (Pengadaan Furnitur Kantor 2026)
 *     Status  : open → untuk demo vendor browsing & join tender baru
 *
 * [D] TENDER DRAFT (Pengadaan ATK 2026)
 *     Status  : draft → tidak tampil di public API (demo filter)
 *
 * Akun demo:
 *   admin@vandrafcy.my.id         / rahasia   (Admin ZETA)
 *   vendor.approved@example.com   / password  (PT Approved Maju — approved)
 *   vendor.kedua@example.com      / password  (CV Karya Prima — approved)
 *   vendor.pending@example.com    / password  (PT Pending Jaya — pending)
 *   vendor.rejected@example.com   / password  (PT Ditolak Jaya — rejected)
 */
class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // ═══════════════════════════════════════════════════════════════════════
        // BAGIAN 1 — USERS & VENDORS
        // ═══════════════════════════════════════════════════════════════════════

        // ─── 1. Admin ─────────────────────────────────────────────────────────
        $admin = User::firstOrCreate(
            ['email' => 'admin@vandrafcy.my.id'],
            [
                'name'     => 'Admin ZETA',
                'password' => Hash::make('rahasia'),
                'role'     => 'admin',
            ]
        );

        // ─── 2. Vendor Pending ────────────────────────────────────────────────
        $userPending = User::firstOrCreate(
            ['email' => 'vendor.pending@example.com'],
            [
                'name'     => 'Vendor Pending',
                'password' => Hash::make('password'),
                'role'     => 'vendor',
            ]
        );

        Vendor::firstOrCreate(
            ['user_id' => $userPending->id],
            [
                'company_name'        => 'PT Pending Jaya',
                'phone'               => '081200000001',
                'address'             => 'Jl. Pending No. 1, Jakarta',
                'verification_status' => 'pending',
            ]
        );

        // ─── 3. Vendor Approved Pertama ───────────────────────────────────────
        $userApproved = User::firstOrCreate(
            ['email' => 'vendor.approved@example.com'],
            [
                'name'     => 'Vendor Approved',
                'password' => Hash::make('password'),
                'role'     => 'vendor',
            ]
        );

        $vendorApproved = Vendor::firstOrCreate(
            ['user_id' => $userApproved->id],
            [
                'company_name'        => 'PT Approved Maju',
                'phone'               => '081200000002',
                'address'             => 'Jl. Maju No. 2, Bandung',
                'verification_status' => 'approved',
                'verified_by'         => $admin->id,
                'verified_at'         => now()->subDays(5),
            ]
        );

        // ─── 4. Dokumen Vendor Pertama ────────────────────────────────────────
        VendorDocument::firstOrCreate(
            ['vendor_id' => $vendorApproved->id, 'document_type' => 'legalitas'],
            [
                'file_name'   => 'akta_pendirian.pdf',
                'file_path'   => 'documents/vendors/' . $vendorApproved->id . '/akta_pendirian.pdf',
                'mime_type'   => 'application/pdf',
                'file_size'   => 204800,
                'uploaded_at' => now()->subDays(6),
            ]
        );

        VendorDocument::firstOrCreate(
            ['vendor_id' => $vendorApproved->id, 'document_type' => 'izin_usaha'],
            [
                'file_name'   => 'siup.pdf',
                'file_path'   => 'documents/vendors/' . $vendorApproved->id . '/siup.pdf',
                'mime_type'   => 'application/pdf',
                'file_size'   => 153600,
                'uploaded_at' => now()->subDays(6),
            ]
        );

        // ─── 5. Vendor Approved Kedua ─────────────────────────────────────────
        $userApproved2 = User::firstOrCreate(
            ['email' => 'vendor.kedua@example.com'],
            [
                'name'     => 'Vendor Kedua',
                'password' => Hash::make('password'),
                'role'     => 'vendor',
            ]
        );

        $vendorApproved2 = Vendor::firstOrCreate(
            ['user_id' => $userApproved2->id],
            [
                'company_name'        => 'CV Karya Prima',
                'phone'               => '081200000003',
                'address'             => 'Jl. Prima No. 3, Surabaya',
                'verification_status' => 'approved',
                'verified_by'         => $admin->id,
                'verified_at'         => now()->subDays(4),
            ]
        );

        // ─── 6. Dokumen Vendor Kedua ──────────────────────────────────────────
        VendorDocument::firstOrCreate(
            ['vendor_id' => $vendorApproved2->id, 'document_type' => 'legalitas'],
            [
                'file_name'   => 'akta_cv_karya_prima.pdf',
                'file_path'   => 'documents/vendors/' . $vendorApproved2->id . '/akta_cv_karya_prima.pdf',
                'mime_type'   => 'application/pdf',
                'file_size'   => 187392,
                'uploaded_at' => now()->subDays(5),
            ]
        );

        VendorDocument::firstOrCreate(
            ['vendor_id' => $vendorApproved2->id, 'document_type' => 'izin_usaha'],
            [
                'file_name'   => 'siup_cv_karya_prima.pdf',
                'file_path'   => 'documents/vendors/' . $vendorApproved2->id . '/siup_cv_karya_prima.pdf',
                'mime_type'   => 'application/pdf',
                'file_size'   => 143360,
                'uploaded_at' => now()->subDays(5),
            ]
        );

        // ─── 7. Vendor Rejected (skenario demo penolakan) ─────────────────────
        $userRejected = User::firstOrCreate(
            ['email' => 'vendor.rejected@example.com'],
            [
                'name'     => 'Vendor Rejected',
                'password' => Hash::make('password'),
                'role'     => 'vendor',
            ]
        );

        Vendor::firstOrCreate(
            ['user_id' => $userRejected->id],
            [
                'company_name'        => 'PT Ditolak Jaya',
                'phone'               => '081200000004',
                'address'             => 'Jl. Ditolak No. 4, Medan',
                'verification_status' => 'rejected',
                'verification_notes'  => 'Dokumen legalitas tidak lengkap dan tidak sesuai persyaratan.',
                'verified_by'         => $admin->id,
                'verified_at'         => now()->subDays(2),
            ]
        );

        // ═══════════════════════════════════════════════════════════════════════
        // BAGIAN 2 — TENDER DRAFT (tidak tampil di public API)
        // ═══════════════════════════════════════════════════════════════════════

        $tenderDraft = Tender::firstOrCreate(
            ['title' => 'Pengadaan Alat Tulis Kantor 2026'],
            [
                'created_by'      => $admin->id,
                'description'     => 'Pengadaan alat tulis kantor untuk kebutuhan operasional tahun 2026.',
                'specification'   => 'Spesifikasi ATK meliputi kertas A4, pulpen, map, dan perlengkapan kantor lainnya.',
                'start_date'      => now()->addDays(30),
                'end_date'        => now()->addDays(60),
                'aanwijzing_date' => null,
                'bidding_start'   => now()->addDays(35),
                'bidding_end'     => now()->addDays(50),
                'status'          => 'draft',
            ]
        );

        TenderHistory::firstOrCreate(
            ['tender_id' => $tenderDraft->id, 'action' => 'tender_created'],
            [
                'actor_id'    => $admin->id,
                'new_status'  => 'draft',
                'description' => 'Tender dibuat oleh admin.',
                'created_at'  => now()->subDay(),
            ]
        );

        // ═══════════════════════════════════════════════════════════════════════
        // BAGIAN 3 — TENDER OPEN (untuk demo vendor join baru)
        // ═══════════════════════════════════════════════════════════════════════

        $tenderOpen = Tender::firstOrCreate(
            ['title' => 'Pengadaan Furnitur Kantor 2026'],
            [
                'created_by'      => $admin->id,
                'description'     => 'Pengadaan meja, kursi, dan lemari arsip untuk kantor baru.',
                'specification'   => 'Minimal 20 meja kerja, 40 kursi ergonomis, 10 lemari arsip besi.',
                'start_date'      => now()->subDay(),
                'end_date'        => now()->addDays(25),
                'aanwijzing_date' => now()->addDays(3),
                'bidding_start'   => now()->addDays(7),
                'bidding_end'     => now()->addDays(18),
                'status'          => 'open',
            ]
        );

        TenderHistory::firstOrCreate(
            ['tender_id' => $tenderOpen->id, 'action' => 'tender_created'],
            [
                'actor_id'    => $admin->id,
                'new_status'  => 'draft',
                'description' => 'Tender dibuat oleh admin.',
                'created_at'  => now()->subDay(),
            ]
        );

        TenderHistory::firstOrCreate(
            ['tender_id' => $tenderOpen->id, 'action' => 'status_changed'],
            [
                'actor_id'    => $admin->id,
                'old_status'  => 'draft',
                'new_status'  => 'open',
                'description' => 'Tender dibuka untuk pendaftaran vendor.',
                'created_at'  => now()->subHours(2),
            ]
        );

        // ═══════════════════════════════════════════════════════════════════════
        // BAGIAN 4 — [A] TENDER BIDDING AKTIF (untuk demo join & bidding)
        // Skenario: Kedua vendor sudah JOIN tapi BELUM ada bid → bisa demo submit bid
        // Timeline bidding sedang aktif (bidding_start lalu, bidding_end depan)
        // TIDAK ada tender_result dan TIDAK ada purchase_order
        // ═══════════════════════════════════════════════════════════════════════

        $tenderBidding = Tender::firstOrCreate(
            ['title' => 'Pengadaan Laptop dan Aksesori 2026'],
            [
                'created_by'      => $admin->id,
                'description'     => 'Pengadaan laptop, mouse, keyboard, dan aksesori pendukung untuk karyawan baru.',
                'specification'   => 'Laptop: Core i5 Gen 13, RAM 16GB, SSD 512GB. Mouse wireless. Keyboard mekanikal. Minimal 15 unit.',
                'start_date'      => now()->subDays(12),
                'end_date'        => now()->addDays(18),
                'aanwijzing_date' => now()->subDays(7),
                'bidding_start'   => now()->subDays(3),  // sudah dimulai 3 hari lalu
                'bidding_end'     => now()->addDays(7),  // masih buka 7 hari ke depan
                'status'          => 'bidding',
            ]
        );

        TenderHistory::firstOrCreate(
            ['tender_id' => $tenderBidding->id, 'action' => 'tender_created'],
            [
                'actor_id'    => $admin->id,
                'new_status'  => 'draft',
                'description' => 'Tender dibuat oleh admin.',
                'created_at'  => now()->subDays(12),
            ]
        );

        TenderHistory::firstOrCreate(
            ['tender_id' => $tenderBidding->id, 'action' => 'status_changed'],
            [
                'actor_id'    => $admin->id,
                'old_status'  => 'draft',
                'new_status'  => 'bidding',
                'description' => 'Tender masuk fase bidding.',
                'created_at'  => now()->subDays(3),
            ]
        );

        // Aanwijzing announcement untuk tender bidding
        TenderAnnouncement::firstOrCreate(
            ['tender_id' => $tenderBidding->id, 'title' => 'Aanwijzing Pengadaan Laptop 2026'],
            [
                'created_by'   => $admin->id,
                'content'      => 'Rapat aanwijzing dilaksanakan via Zoom pada tanggal ' . now()->subDays(7)->format('d M Y') . '. Seluruh vendor yang terdaftar wajib mengikuti sesi ini.',
                'published_at' => now()->subDays(7),
            ]
        );

        // Vendor pertama join tender bidding
        $pBidding1 = TenderParticipant::firstOrCreate(
            ['tender_id' => $tenderBidding->id, 'vendor_id' => $vendorApproved->id],
            ['joined_at' => now()->subDays(10)]
        );

        TenderHistory::firstOrCreate(
            ['tender_id' => $tenderBidding->id, 'action' => 'vendor_joined', 'actor_id' => $userApproved->id],
            [
                'description' => 'Vendor ' . $vendorApproved->company_name . ' bergabung ke tender.',
                'created_at'  => $pBidding1->joined_at,
            ]
        );

        // Vendor kedua join tender bidding
        $pBidding2 = TenderParticipant::firstOrCreate(
            ['tender_id' => $tenderBidding->id, 'vendor_id' => $vendorApproved2->id],
            ['joined_at' => now()->subDays(9)]
        );

        TenderHistory::firstOrCreate(
            ['tender_id' => $tenderBidding->id, 'action' => 'vendor_joined', 'actor_id' => $userApproved2->id],
            [
                'description' => 'Vendor ' . $vendorApproved2->company_name . ' bergabung ke tender.',
                'created_at'  => $pBidding2->joined_at,
            ]
        );

        // CATATAN: Tidak ada Bid, TenderResult, atau PurchaseOrder untuk tenderBidding.
        // Ini adalah skenario demo: vendor bisa submit bid via API.

        // ═══════════════════════════════════════════════════════════════════════
        // BAGIAN 5 — [B] TENDER FINISHED (untuk demo result / winner / PO)
        // Skenario: Kedua vendor sudah join + bid. PT Approved Maju menang.
        // Ada tender_result dan purchase_order.
        // ═══════════════════════════════════════════════════════════════════════

        $tenderFinished = Tender::firstOrCreate(
            ['title' => 'Pengadaan Komputer dan Perangkat IT 2026'],
            [
                'created_by'      => $admin->id,
                'description'     => 'Pengadaan komputer, laptop, dan perangkat IT untuk kebutuhan kantor pusat.',
                'specification'   => 'Spesifikasi: Desktop Core i5 Gen 12, RAM 16GB, SSD 512GB, minimal 10 unit.',
                'start_date'      => now()->subDays(30),
                'end_date'        => now()->subDays(5),
                'aanwijzing_date' => now()->subDays(25),
                'bidding_start'   => now()->subDays(20),
                'bidding_end'     => now()->subDays(10),
                'status'          => 'finished',
            ]
        );

        TenderHistory::firstOrCreate(
            ['tender_id' => $tenderFinished->id, 'action' => 'tender_created'],
            [
                'actor_id'    => $admin->id,
                'new_status'  => 'draft',
                'description' => 'Tender dibuat oleh admin.',
                'created_at'  => now()->subDays(30),
            ]
        );

        TenderHistory::firstOrCreate(
            ['tender_id' => $tenderFinished->id, 'action' => 'status_changed', 'actor_id' => $admin->id],
            [
                'old_status'  => 'draft',
                'new_status'  => 'bidding',
                'description' => 'Tender masuk fase bidding.',
                'created_at'  => now()->subDays(20),
            ]
        );

        // Aanwijzing announcement
        TenderAnnouncement::firstOrCreate(
            ['tender_id' => $tenderFinished->id, 'title' => 'Aanwijzing Pengadaan Komputer IT 2026'],
            [
                'created_by'   => $admin->id,
                'content'      => 'Rapat aanwijzing dilaksanakan secara online. Dokumen pengadaan tersedia di portal. Seluruh vendor yang terdaftar wajib mengikuti.',
                'published_at' => now()->subDays(25),
            ]
        );

        // Vendor pertama join tender finished
        $pFinished1 = TenderParticipant::firstOrCreate(
            ['tender_id' => $tenderFinished->id, 'vendor_id' => $vendorApproved->id],
            ['joined_at' => now()->subDays(28)]
        );

        TenderHistory::firstOrCreate(
            ['tender_id' => $tenderFinished->id, 'action' => 'vendor_joined', 'actor_id' => $userApproved->id],
            [
                'description' => 'Vendor ' . $vendorApproved->company_name . ' bergabung ke tender.',
                'created_at'  => $pFinished1->joined_at,
            ]
        );

        // Vendor kedua join tender finished
        $pFinished2 = TenderParticipant::firstOrCreate(
            ['tender_id' => $tenderFinished->id, 'vendor_id' => $vendorApproved2->id],
            ['joined_at' => now()->subDays(27)]
        );

        TenderHistory::firstOrCreate(
            ['tender_id' => $tenderFinished->id, 'action' => 'vendor_joined', 'actor_id' => $userApproved2->id],
            [
                'description' => 'Vendor ' . $vendorApproved2->company_name . ' bergabung ke tender.',
                'created_at'  => $pFinished2->joined_at,
            ]
        );

        // Bid vendor pertama (PT Approved Maju) — MENANG
        $bidF1 = Bid::firstOrCreate(
            ['tender_id' => $tenderFinished->id, 'vendor_id' => $vendorApproved->id],
            [
                'bid_amount'   => 92500000.00,
                'notes'        => 'Penawaran termasuk garansi 1 tahun dan jasa instalasi.',
                'submitted_at' => now()->subDays(15),
            ]
        );

        BidHistory::firstOrCreate(
            ['bid_id' => $bidF1->id, 'old_bid_amount' => null],
            [
                'tender_id'      => $tenderFinished->id,
                'vendor_id'      => $vendorApproved->id,
                'new_bid_amount' => 92500000.00,
                'notes'          => 'Penawaran awal PT Approved Maju.',
                'changed_at'     => now()->subDays(15),
                'created_at'     => now()->subDays(15),
            ]
        );

        TenderHistory::firstOrCreate(
            ['tender_id' => $tenderFinished->id, 'action' => 'bid_submitted', 'actor_id' => $userApproved->id],
            [
                'description' => 'PT Approved Maju mengajukan penawaran senilai Rp 92.500.000.',
                'metadata'    => ['bid_id' => $bidF1->id, 'bid_amount' => 92500000.00],
                'created_at'  => now()->subDays(15),
            ]
        );

        // Bid vendor kedua (CV Karya Prima) — KALAH
        $bidF2 = Bid::firstOrCreate(
            ['tender_id' => $tenderFinished->id, 'vendor_id' => $vendorApproved2->id],
            [
                'bid_amount'   => 97000000.00,
                'notes'        => 'Penawaran termasuk garansi 6 bulan dan pelatihan penggunaan.',
                'submitted_at' => now()->subDays(14),
            ]
        );

        BidHistory::firstOrCreate(
            ['bid_id' => $bidF2->id, 'old_bid_amount' => null],
            [
                'tender_id'      => $tenderFinished->id,
                'vendor_id'      => $vendorApproved2->id,
                'new_bid_amount' => 97000000.00,
                'notes'          => 'Penawaran awal CV Karya Prima.',
                'changed_at'     => now()->subDays(14),
                'created_at'     => now()->subDays(14),
            ]
        );

        TenderHistory::firstOrCreate(
            ['tender_id' => $tenderFinished->id, 'action' => 'bid_submitted', 'actor_id' => $userApproved2->id],
            [
                'description' => 'CV Karya Prima mengajukan penawaran senilai Rp 97.000.000.',
                'metadata'    => ['bid_id' => $bidF2->id, 'bid_amount' => 97000000.00],
                'created_at'  => now()->subDays(14),
            ]
        );

        // Tender result — PT Approved Maju menang (harga terendah)
        $result = TenderResult::firstOrCreate(
            ['tender_id' => $tenderFinished->id],
            [
                'winner_vendor_id'   => $vendorApproved->id,
                'winning_bid_id'     => $bidF1->id,
                'winning_bid_amount' => 92500000.00,
                'selection_method'   => 'lowest_price',
                'notes'              => 'PT Approved Maju terpilih karena menawarkan harga terendah dengan spesifikasi sesuai.',
                'decided_by'         => $admin->id,
                'decided_at'         => now()->subDays(8),
            ]
        );

        TenderHistory::firstOrCreate(
            ['tender_id' => $tenderFinished->id, 'action' => 'winner_selected'],
            [
                'actor_id'    => $admin->id,
                'old_status'  => 'bidding',
                'new_status'  => 'finished',
                'description' => 'Pemenang dipilih: ' . $vendorApproved->company_name . ' — Rp 92.500.000.',
                'metadata'    => [
                    'winner_vendor_id'    => $vendorApproved->id,
                    'winning_bid_amount'  => 92500000.00,
                ],
                'created_at'  => now()->subDays(8),
            ]
        );

        // Purchase Order
        $po = PurchaseOrder::firstOrCreate(
            ['tender_id' => $tenderFinished->id],
            [
                'tender_result_id' => $result->id,
                'vendor_id'        => $vendorApproved->id,
                'po_number'        => 'PO-2026-IT-001',
                'amount'           => 92500000.00,
                'issued_date'      => now()->subDays(7)->toDateString(),
                'notes'            => 'Purchase Order pengadaan komputer dan perangkat IT kantor pusat.',
                'generated_by'     => $admin->id,
            ]
        );

        TenderHistory::firstOrCreate(
            ['tender_id' => $tenderFinished->id, 'action' => 'po_generated'],
            [
                'actor_id'    => $admin->id,
                'description' => 'Purchase Order ' . $po->po_number . ' diterbitkan.',
                'metadata'    => ['po_id' => $po->id, 'po_number' => $po->po_number],
                'created_at'  => now()->subDays(7),
            ]
        );
    }
}
