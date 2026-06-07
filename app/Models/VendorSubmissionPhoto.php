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

    /**
     * URL publik foto — selalu di-generate ulang dari photo_path
     * agar menggunakan APP_URL yang benar, bukan nilai kolom photo_url
     * yang mungkin di-insert dari mobile dengan host berbeda (misal: localhost).
     */
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
