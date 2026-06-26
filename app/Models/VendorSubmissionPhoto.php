<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
class VendorSubmissionPhoto extends Model
{
    protected $fillable = [
        'vendor_submission_id',
        'photo_path',
        'photo_url',
    ];
    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo_path
            ? Storage::disk('public')->url($this->photo_path)
            : ($this->attributes['photo_url'] ?? null);
    }
    public function submission(): BelongsTo
    {
        return $this->belongsTo(VendorSubmission::class, 'vendor_submission_id');
    }
}
