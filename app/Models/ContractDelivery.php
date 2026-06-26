<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class ContractDelivery extends Model {
    protected $fillable = ['contract_id','milestone_name','description','due_date','status','vendor_notes','evidence_path','delivered_at','verified_at','verified_by'];
    protected function casts(): array { return ['due_date'=>'date','delivered_at'=>'datetime','verified_at'=>'datetime']; }
    public function contract(): BelongsTo { return $this->belongsTo(Contract::class); }
    public function verifier(): BelongsTo { return $this->belongsTo(User::class,'verified_by'); }
}
