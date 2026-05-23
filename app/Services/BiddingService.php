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
     * Validate that a vendor is approved and has joined the tender.
     *
     * @throws \RuntimeException
     */
    public function assertVendorCanBid(Vendor $vendor, Tender $tender): void
    {
        if ($vendor->verification_status !== 'approved') {
            throw new \RuntimeException('Vendor belum diverifikasi.', 403);
        }

        $joined = TenderParticipant::where('tender_id', $tender->id)
            ->where('vendor_id', $vendor->id)
            ->exists();

        if (!$joined) {
            throw new \RuntimeException('Vendor belum bergabung pada tender ini.', 422);
        }
    }

    /**
     * Assert that the tender is currently in its bidding window.
     *
     * @throws \RuntimeException
     */
    public function assertBiddingOpen(Tender $tender): void
    {
        $now = now();

        if ($tender->status !== 'bidding') {
            throw new \RuntimeException('Tender tidak dalam status bidding.', 422);
        }

        if ($tender->bidding_start && $now->lt($tender->bidding_start)) {
            throw new \RuntimeException('Periode bidding belum dimulai.', 422);
        }

        if ($tender->bidding_end && $now->gt($tender->bidding_end)) {
            throw new \RuntimeException('Periode bidding sudah berakhir.', 422);
        }
    }

    /**
     * Submit a new bid.
     * FIX HIGH-03: Gunakan lockForUpdate di dalam transaction untuk mencegah race condition
     * saat dua request paralel dari vendor yang sama mencoba submit bid bersamaan.
     */
    public function submitBid(Vendor $vendor, Tender $tender, float $amount, ?string $notes = null): Bid
    {
        return DB::transaction(function () use ($vendor, $tender, $amount, $notes) {
            // Cek duplikat dengan pessimistic lock agar atomik
            $exists = Bid::where('tender_id', $tender->id)
                ->where('vendor_id', $vendor->id)
                ->lockForUpdate()
                ->exists();

            if ($exists) {
                throw new \RuntimeException('Anda sudah memiliki bid pada tender ini. Gunakan endpoint update.', 422);
            }

            $bid = Bid::create([
                'tender_id'    => $tender->id,
                'vendor_id'    => $vendor->id,
                'bid_amount'   => $amount,
                'notes'        => $notes,
                'submitted_at' => now(),
            ]);

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

            $this->historyService->log(
                tenderId:    $tender->id,
                actorId:     $vendor->user_id,
                action:      'bid_submitted',
                oldStatus:   $tender->status,
                newStatus:   $tender->status,
                description: "Bid Rp " . number_format($amount, 0, ',', '.') . " diajukan oleh {$vendor->company_name}."
            );

            return $bid;
        });
    }

    /**
     * Update an existing bid.
     */
    public function updateBid(Bid $bid, Vendor $vendor, Tender $tender, float $amount, ?string $notes = null): Bid
    {
        return DB::transaction(function () use ($bid, $vendor, $tender, $amount, $notes) {
            $old = (float) $bid->bid_amount;

            $bid->update([
                'bid_amount' => $amount,
                'notes'      => $notes,
            ]);

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

            $this->historyService->log(
                tenderId:    $tender->id,
                actorId:     $vendor->user_id,
                action:      'bid_updated',
                oldStatus:   $tender->status,
                newStatus:   $tender->status,
                description: "Bid diperbarui: Rp " . number_format($old, 0, ',', '.') .
                             " → Rp " . number_format($amount, 0, ',', '.') .
                             " oleh {$vendor->company_name}."
            );

            return $bid->fresh();
        });
    }
}
