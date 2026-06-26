<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class TenderComplaint extends Model {
    protected $fillable = ['tender_id','vendor_id','type','reason','supporting_docs','status','response','responded_by','responded_at','deadline'];
    protected function casts(): array {
        return ['responded_at'=>'datetime','deadline'=>'datetime','supporting_docs'=>'array'];
    }
    public function tender(): BelongsTo { return $this->belongsTo(Tender::class); }
    public function vendor(): BelongsTo { return $this->belongsTo(Vendor::class); }
    public function responder(): BelongsTo { return $this->belongsTo(User::class,'responded_by'); }
    public function isExpired(): bool { return $this->deadline && now()->gt($this->deadline); }
}
