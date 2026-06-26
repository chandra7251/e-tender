<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class TenderItem extends Model {
    protected $fillable = ['tender_id','description','unit','quantity','hps_unit_price','sort_order'];
    protected function casts(): array { return ['quantity'=>'float','hps_unit_price'=>'float','hps_subtotal'=>'float']; }
    public function tender(): BelongsTo { return $this->belongsTo(Tender::class); }
    public function bidItems(): HasMany { return $this->hasMany(BidItem::class); }
}
