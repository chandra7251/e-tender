<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorSubmissionPhoto extends Model
{
    protected $fillable = [
        'vendor_submission_id',
        'photo_path',
        'photo_url',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(VendorSubmission::class, 'vendor_submission_id');
    }
}
