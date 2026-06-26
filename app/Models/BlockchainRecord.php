<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlockchainRecord extends Model
{
    protected $fillable = [
        'tender_id','event_type','payload_hash','block_hash',
        'prev_hash','payload','network','tx_hash','verified'
    ];
    protected $casts = ['verified' => 'boolean'];
    public function tender(): BelongsTo { return $this->belongsTo(Tender::class); }
}
