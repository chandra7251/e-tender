<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
class TenderAnnouncement extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'tender_id',
        'created_by',
        'title',
        'content',
        'published_at',
    ];
    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }
    public function tender(): BelongsTo
    {
        return $this->belongsTo(Tender::class);
    }
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
