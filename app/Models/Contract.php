<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
class Contract extends Model {
    use SoftDeletes;
    protected $fillable = ['contract_number','tender_id','vendor_id','created_by','status','contract_value','start_date','end_date','terms','vendor_signature_path','vendor_signed_at','admin_signature_path','admin_signed_at','document_hash','qr_code_path','termination_reason'];
    protected function casts(): array {
        return ['contract_value'=>'float','start_date'=>'date','end_date'=>'date','vendor_signed_at'=>'datetime','admin_signed_at'=>'datetime'];
    }
    public function tender(): BelongsTo { return $this->belongsTo(Tender::class); }
    public function vendor(): BelongsTo { return $this->belongsTo(Vendor::class); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class,'created_by'); }
    public function deliveries(): HasMany { return $this->hasMany(ContractDelivery::class); }
    public static function generateNumber(int $tenderId): string {
        $year = now()->format('Y');
        $month = now()->format('m');
        $seq = str_pad(static::whereYear('created_at',now()->year)->count()+1, 3, '0', STR_PAD_LEFT);
        return "KONTRAK-ZETA/{$month}/{$year}/{$seq}";
    }
}
