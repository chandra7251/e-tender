<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class VendorCertification extends Model {
    protected $fillable = ['vendor_id','name','issuer','certificate_number','issued_at','expires_at','file_path'];
    protected function casts(): array { return ['issued_at'=>'date','expires_at'=>'date']; }
    public function vendor(): BelongsTo { return $this->belongsTo(Vendor::class); }
    public function isExpired(): bool { return $this->expires_at && now()->gt($this->expires_at); }
}
