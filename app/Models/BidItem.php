<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class BidItem extends Model {
    protected $fillable = ['bid_id','tender_item_id','unit_price','subtotal'];
    protected function casts(): array {
        return ['unit_price' => 'float', 'subtotal' => 'float'];
    }
    protected static function booted(): void {
        static::saving(function (BidItem $item) {
            // Hitung subtotal otomatis dari unit_price * tender_item.quantity
            if ($item->tenderItem) {
                $item->subtotal = $item->unit_price * $item->tenderItem->quantity;
            }
        });
    }
    public function bid(): BelongsTo { return $this->belongsTo(Bid::class); }
    public function tenderItem(): BelongsTo { return $this->belongsTo(TenderItem::class); }
}
