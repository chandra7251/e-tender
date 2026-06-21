<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
class PurchaseOrder extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'tender_result_id',
        'tender_id',
        'vendor_id',
        'po_number',
        'amount',
        'issued_date',
        'notes',
        'generated_by',
    ];
    protected function casts(): array
    {
        return [
            'amount'      => 'decimal:2',
            'issued_date' => 'date',
        ];
    }
    public function tenderResult(): BelongsTo
    {
        return $this->belongsTo(TenderResult::class);
    }
    public function tender(): BelongsTo
    {
        return $this->belongsTo(Tender::class);
    }
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }
    public function generator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
}
