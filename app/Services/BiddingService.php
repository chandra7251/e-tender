<?php
namespace App\Services;
use App\Models\Bid;
use App\Models\BidHistory;
use App\Models\Tender;
use App\Models\TenderParticipant;
use App\Models\BidItem;
use App\Models\Vendor;
use Illuminate\Support\Facades\DB;
class BiddingService
{
    public function __construct(protected TenderHistoryService $historyService) {}
    public function assertVendorCanBid(Vendor $vendor, Tender $tender): void
    {
        if ($vendor->is_blacklisted) {
            throw new \RuntimeException('Akun Anda diblokir (blacklist). Hubungi admin.', 403);
        }
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
    public function submitBid(Vendor $vendor, Tender $tender, float $amount, ?string $notes = null, array $items = []): Bid
    {
        return DB::transaction(function () use ($vendor, $tender, $amount, $notes, $items) {
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
            // Simpan BidItems jika ada
            if (!empty($items)) {
                foreach ($items as $item) {
                    BidItem::create([
                        'bid_id'         => $bid->id,
                        'tender_item_id' => $item['tender_item_id'],
                        'unit_price'     => $item['unit_price'],
                    ]);
                }
            }
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
                description: 'Bid Rp ' . number_format($amount, 0, ',', '.') . " diajukan oleh {$vendor->company_name}."
            );
            return $bid;
        });
    }
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
                description: 'Bid diperbarui: Rp ' . number_format($old, 0, ',', '.') .
                             ' → Rp ' . number_format($amount, 0, ',', '.') .
                             " oleh {$vendor->company_name}."
            );
            return $bid->fresh();
        });
    }
}
