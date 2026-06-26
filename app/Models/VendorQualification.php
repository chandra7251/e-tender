<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class VendorQualification extends Model {
    protected $fillable = ['vendor_id','kbli_code','kbli_name','business_scale','npwp','siup_number','tdp_number','siup_expires_at'];
    protected function casts(): array { return ['siup_expires_at'=>'date']; }
    public function vendor(): BelongsTo { return $this->belongsTo(Vendor::class); }
}
