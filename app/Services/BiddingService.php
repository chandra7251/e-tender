<?php

namespace App\Services;

use App\Models\Bid;
use App\Models\BidHistory;
use App\Models\Tender;
use App\Models\TenderParticipant;
use App\Models\Vendor;
use Illuminate\Support\Facades\DB;

class BiddingService
{
    public function __construct(protected TenderHistoryService $historyService) {}

    /**
     * Validasi bahwa vendor sudah disetujui admin dan sudah terdaftar di tender.
     * Dipanggil sebelum submit atau update bid.
     *
     * @throws \RuntimeException dengan kode HTTP yang sesuai (403 atau 422)
     */
    public function assertVendorCanBid(Vendor $vendor, Tender $tender): void
    {
        // Vendor harus sudah berstatus 'approved' dari admin
        if ($vendor->verification_status !== 'approved') {
            throw new \RuntimeException('Vendor belum diverifikasi.', 403);
        }

        // Vendor harus sudah join tender ini sebelum bisa bid
        $joined = TenderParticipant::where('tender_id', $tender->id)
            ->where('vendor_id', $vendor->id)
            ->exists();

        if (!$joined) {
            throw new \RuntimeException('Vendor belum bergabung pada tender ini.', 422);
        }
    }

    /**
     * Validasi bahwa tender sedang dalam periode bidding yang aktif.
     * Cek status tender DAN rentang waktu bidding_start-bidding_end.
     *
     * @throws \RuntimeException dengan kode HTTP 422
     */
    public function assertBiddingOpen(Tender $tender): void
    {
        $now = now();

        // Status tender harus 'bidding'
        if ($tender->status !== 'bidding') {
            throw new \RuntimeException('Tender tidak dalam status bidding.', 422);
        }

        // Waktu sekarang harus sudah melewati bidding_start (jika di-set)
        if ($tender->bidding_start && $now->lt($tender->bidding_start)) {
            throw new \RuntimeException('Periode bidding belum dimulai.', 422);
        }

        // Waktu sekarang harus belum melewati bidding_end (jika di-set)
        if ($tender->bidding_end && $now->gt($tender->bidding_end)) {
            throw new \RuntimeException('Periode bidding sudah berakhir.', 422);
        }
    }

    /**
     * Submit bid baru dari vendor ke tender.
     * Menggunakan DB transaction + pessimistic lock (lockForUpdate) untuk
     * mencegah race condition jika dua request submit masuk bersamaan.
     */
    public function submitBid(Vendor $vendor, Tender $tender, float $amount, ?string $notes = null): Bid
    {
        return DB::transaction(function () use ($vendor, $tender, $amount, $notes) {
            // Lock row untuk mencegah duplikasi bid akibat concurrent request
            $exists = Bid::where('tender_id', $tender->id)
                ->where('vendor_id', $vendor->id)
                ->lockForUpdate()
                ->exists();

            if ($exists) {
                throw new \RuntimeException('Anda sudah memiliki bid pada tender ini. Gunakan endpoint update.', 422);
            }

            // Buat record bid baru
            $bid = Bid::create([
                'tender_id'    => $tender->id,
                'vendor_id'    => $vendor->id,
                'bid_amount'   => $amount,
                'notes'        => $notes,
                'submitted_at' => now(),
            ]);

            // Catat di bid history sebagai snapshot immutable (old_bid_amount null = pertama kali)
            BidHistory::create([
                'bid_id'         => $bid->id,
                'tender_id'      => $tender->id,
                'vendor_id'      => $vendor->id,
                'old_bid_amount' => null,
                'new_bid_amount' => $amount,
                'notes'          => $notes,
                'changed_at'     => now(),
                'created_at'     => now(),
            ]);

            // Catat di tender history untuk audit trail aktivitas tender
            $this->historyService->log(
                tenderId:    $tender->id,
                actorId:     $vendor->user_id,
                action:      'bid_submitted',
                oldStatus:   $tender->status,
                newStatus:   $tender->status,
                description: 'Bid Rp ' . number_format($amount, 0, ',', '.') . " diajukan oleh {$vendor->company_name}."
            );

            return $bid;
        });
    }

    /**
     * Perbarui nilai bid yang sudah ada.
     * Menyimpan riwayat perubahan harga sebelum dan sesudah di BidHistory.
     */
    public function updateBid(Bid $bid, Vendor $vendor, Tender $tender, float $amount, ?string $notes = null): Bid
    {
        return DB::transaction(function () use ($bid, $vendor, $tender, $amount, $notes) {
            $old = (float) $bid->bid_amount; // Simpan nilai lama sebelum diupdate

            $bid->update([
                'bid_amount' => $amount,
                'notes'      => $notes,
            ]);

            // Catat perubahan harga untuk keperluan audit dan riwayat bid
            BidHistory::create([
                'bid_id'         => $bid->id,
                'tender_id'      => $tender->id,
                'vendor_id'      => $vendor->id,
                'old_bid_amount' => $old,
                'new_bid_amount' => $amount,
                'notes'          => $notes,
                'changed_at'     => now(),
                'created_at'     => now(),
            ]);

            // Catat di tender history — mencantumkan perubahan harga sebelum dan sesudah
            $this->historyService->log(
                tenderId:    $tender->id,
                actorId:     $vendor->user_id,
                action:      'bid_updated',
                oldStatus:   $tender->status,
                newStatus:   $tender->status,
                description: 'Bid diperbarui: Rp ' . number_format($old, 0, ',', '.') .
                             ' → Rp ' . number_format($amount, 0, ',', '.') .
                             " oleh {$vendor->company_name}."
            );

            // fresh() dipanggil agar data yang dikembalikan benar-benar dari DB (bukan cache model)
            return $bid->fresh();
        });
    }
}
