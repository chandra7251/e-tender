<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class TenderHistory extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'tender_id',
        'actor_id',
        'action',
        'old_status',
        'new_status',
        'description',
        'metadata',
        'created_at',
    ];
    protected function casts(): array
    {
        return [
            'metadata'   => 'array',
            'created_at' => 'datetime',
        ];
    }
    public function tender(): BelongsTo
    {
        return $this->belongsTo(Tender::class);
    }
    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}
