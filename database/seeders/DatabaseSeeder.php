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
        // BAGIAN 1 — USERS & VENDORS

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

        // BAGIAN 2 — TENDER DRAFT (tidak tampil di public API)

        $tenderDraft = Tender::firstOrCreate(
            ['title' => 'Penyediaan Lisensi Perangkat Lunak Keamanan Siber (Endpoint Protection) TA 2026'],
            [
                'created_by'      => $admin->id,
                'description'     => 'Pengadaan lisensi tahunan perangkat lunak keamanan siber (Endpoint Protection) untuk 1.500 perangkat karyawan di seluruh cabang, mencakup fitur anti-ransomware, perlindungan ancaman zero-day, dan manajemen terpusat berbasis cloud.',
                'specification'   => '1. Dukungan perlindungan Next-Gen Antivirus (NGAV) & EDR. 2. Lisensi aktif selama 12 bulan penuh sejak BAST. 3. SLA Resolusi 24/7. 4. Kompatibilitas multi-platform (Windows, macOS, Linux).',
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

        // BAGIAN 3 — TENDER OPEN (untuk demo vendor join baru

        $tenderOpen = Tender::firstOrCreate(
            ['title' => 'Pengadaan Kendaraan Operasional Lapangan 4x4 (Double Cabin) Batch I'],
            [
                'created_by'      => $admin->id,
                'description'     => 'Pengadaan 10 unit kendaraan operasional lapangan jenis Double Cabin 4x4 untuk mendukung mobilitas tim teknisi lapangan di area operasional tambang dan perkebunan.',
                'specification'   => '1. Mesin Diesel min. 2400cc VGT. 2. Transmisi manual 6-percepatan 4WD. 3. Warna putih standar perusahaan. 4. Dilengkapi roll bar, bedliner, dan APAR. 5. Termasuk biaya STNK, BPKB, dan KIR.',
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

        // BAGIAN 4 — [A] TENDER BIDDING AKTIF (untuk demo join & bidding)
        $tenderBidding = Tender::firstOrCreate(
            ['title' => 'Pembangunan Infrastruktur Jaringan Fiber Optic dan Integrasi Data Center'],
            [
                'created_by'      => $admin->id,
                'description'     => 'Proyek penarikan kabel Fiber Optic (FO) sepanjang 15 KM untuk menghubungkan 3 gedung utama, beserta pengadaan perangkat aktif jaringan (Core Switch, Router) dan integrasi ke Data Center terpusat.',
                'specification'   => '1. Kabel FO Single-Mode 24 Core (Underground & Aerial). 2. Core Switch Layer 3 min. 48 Port SFP+. 3. Instalasi, terminasi, dan sertifikasi OTDR. 4. Garansi perangkat aktif minimal 3 tahun (NBD Replacement).',
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


        // BAGIAN 5 — [B] TENDER FINISHED (untuk demo result / winner / PO)
        // Skenario: Kedua vendor sudah join + bid. PT Approved Maju menang.

        $tenderFinished = Tender::firstOrCreate(
            ['title' => 'Pengadaan Perangkat Komputasi Workstation untuk Divisi Riset & Pengembangan'],
            [
                'created_by'      => $admin->id,
                'description'     => 'Pengadaan 25 unit High-Performance Workstation untuk kebutuhan rendering 3D, simulasi data, dan pemrosesan AI di divisi Riset dan Pengembangan (R&D).',
                'specification'   => '1. Prosesor min. 16 Cores / 32 Threads. 2. RAM 128GB DDR5 ECC. 3. GPU RTX 4090 atau setara (VRAM 24GB). 4. Storage 2x 2TB NVMe Gen4 (RAID 1). 5. Termasuk monitor profesional kalibrasi warna 27-inch 4K.',
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

        // 6 TENDER DUMMY UNTUK DEMO

        $tender5 = Tender::firstOrCreate(
            ['title' => 'Pembangunan Fasilitas Pengolahan Air Bersih (Water Treatment Plant) Area Pabrik'],
            [
                'created_by'      => $admin->id,
                'description'     => 'Proyek EPC (Engineering, Procurement, and Construction) untuk pembangunan Water Treatment Plant (WTP) dengan kapasitas 50 liter/detik untuk memenuhi kebutuhan operasional pabrik.',
                'specification'   => 'Lingkup kerja meliputi desain sipil, instalasi perpipaan, pengadaan pompa distribusi, dan sistem filtrasi RO (Reverse Osmosis). Target penyelesaian 6 bulan.',
                'start_date'      => now()->addDays(5),
                'end_date'        => now()->addDays(45),
                'aanwijzing_date' => now()->addDays(10),
                'bidding_start'   => now()->addDays(15),
                'bidding_end'     => now()->addDays(30),
                'status'          => 'open',
            ]
        );

        $tender6 = Tender::firstOrCreate(
            ['title' => 'Pengadaan Layanan Jasa Konsultan Audit Keuangan Independen Tahun Buku 2026'],
            [
                'created_by'      => $admin->id,
                'description'     => 'Pengadaan jasa konsultan akuntan publik (KAP) tier-1 atau tier-2 untuk melakukan audit laporan keuangan konsolidasi perusahaan periode tahun buku 2026.',
                'specification'   => 'KAP harus terdaftar di OJK dan BPK. Memiliki pengalaman audit di industri manufaktur minimal 10 tahun. Laporan audit selesai maksimal minggu pertama Maret 2027.',
                'start_date'      => now()->addDays(2),
                'end_date'        => now()->addDays(20),
                'aanwijzing_date' => now()->addDays(5),
                'bidding_start'   => now()->addDays(8),
                'bidding_end'     => now()->addDays(15),
                'status'          => 'open',
            ]
        );

        $tender7 = Tender::firstOrCreate(
            ['title' => 'Revitalisasi Sistem Tata Udara (HVAC) Gedung Perkantoran Pusat'],
            [
                'created_by'      => $admin->id,
                'description'     => 'Penggantian chiller dan sistem AHU lama dengan teknologi VRV/VRF yang lebih hemat energi untuk gedung perkantoran 15 lantai.',
                'specification'   => 'Kapasitas pendinginan total min. 500 PK. Teknologi Inverter. Menggunakan refrigeran ramah lingkungan (R32). Kontrak termasuk instalasi, pemipaan, dan maintenance 2 tahun.',
                'start_date'      => now()->addDays(10),
                'end_date'        => now()->addDays(40),
                'aanwijzing_date' => now()->addDays(15),
                'bidding_start'   => now()->addDays(20),
                'bidding_end'     => now()->addDays(35),
                'status'          => 'open',
            ]
        );

        $tender8 = Tender::firstOrCreate(
            ['title' => 'Pengadaan Mesin Cetak Rotogravure 8 Warna untuk Divisi Packaging'],
            [
                'created_by'      => $admin->id,
                'description'     => 'Pengadaan 1 unit mesin cetak rotogravure 8 warna kecepatan tinggi untuk meningkatkan kapasitas produksi kemasan fleksibel (flexible packaging).',
                'specification'   => 'Kecepatan cetak min. 250 m/menit. Lebar web min. 1000 mm. Dilengkapi sistem Auto Splicer dan ARC (Automatic Register Control). Garansi mesin 12 bulan.',
                'start_date'      => now()->addDays(20),
                'end_date'        => now()->addDays(60),
                'aanwijzing_date' => null,
                'bidding_start'   => now()->addDays(25),
                'bidding_end'     => now()->addDays(50),
                'status'          => 'draft',
            ]
        );

        $tender9 = Tender::firstOrCreate(
            ['title' => 'Penyediaan Jasa Katering Karyawan Pabrik Kapasitas 2.000 Porsi/Hari'],
            [
                'created_by'      => $admin->id,
                'description'     => 'Tender kontrak penyediaan makan siang untuk 2.000 karyawan shift pagi dan siang di area pabrik selama periode 1 tahun.',
                'specification'   => 'Penyedia wajib memiliki sertifikat Laik Higiene Sanitasi Jasaboga. Menu bervariasi dengan standar kalori 700 kcal per porsi. Sistem prasmanan dan box.',
                'start_date'      => now()->addDays(1),
                'end_date'        => now()->addDays(14),
                'aanwijzing_date' => now()->addDays(3),
                'bidding_start'   => now()->addDays(5),
                'bidding_end'     => now()->addDays(12),
                'status'          => 'open',
            ]
        );

        $tender10 = Tender::firstOrCreate(
            ['title' => 'Sewa Jangka Panjang Alat Berat (Excavator & Bulldozer) Site Pertambangan'],
            [
                'created_by'      => $admin->id,
                'description'     => 'Pengadaan sewa jangka panjang (2 tahun) alat berat berupa 5 unit Excavator kelas 30-ton dan 2 unit Bulldozer kelas 20-ton untuk operasional di site tambang.',
                'specification'   => 'Tahun pembuatan alat berat min. 2024. Harga sewa sudah termasuk biaya maintenance rutin, mekanik standby di site, dan asuransi all-risk. Tidak termasuk BBM dan Operator.',
                'start_date'      => now()->addDays(7),
                'end_date'        => now()->addDays(30),
                'aanwijzing_date' => now()->addDays(12),
                'bidding_start'   => now()->addDays(15),
                'bidding_end'     => now()->addDays(25),
                'status'          => 'open',
            ]
        );
    }
}
