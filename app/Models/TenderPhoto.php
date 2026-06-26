<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
class TenderPhoto extends Model
{
    protected $fillable = [
        'tender_id',
        'photo_path',
    ];
    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo_path
            ? Storage::disk('public')->url($this->photo_path)
            : null;
    }
    public function tender(): BelongsTo
    {
        return $this->belongsTo(Tender::class);
    }
}
