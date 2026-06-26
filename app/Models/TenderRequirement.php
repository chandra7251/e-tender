<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class TenderRequirement extends Model {
    protected $fillable = ['tender_id','type','value','description'];
    public function tender(): BelongsTo { return $this->belongsTo(Tender::class); }
}
