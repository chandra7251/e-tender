<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenderPayment extends Model
{
    protected $fillable = [
        'tender_id','vendor_id','contract_id','order_id','type',
        'amount','status','snap_token','snap_url','midtrans_data',
        'paid_at','refunded_at'
    ];
    protected $casts = ['midtrans_data' => 'array', 'paid_at' => 'datetime', 'refunded_at' => 'datetime'];
    public function tender(): BelongsTo  { return $this->belongsTo(Tender::class); }
    public function vendor(): BelongsTo  { return $this->belongsTo(Vendor::class); }
    public function contract(): BelongsTo { return $this->belongsTo(Contract::class); }
}
