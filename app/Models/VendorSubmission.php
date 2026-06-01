<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VendorSubmission extends Model
{
    protected $fillable = [
        'vendor_id',
        'nama_barang',
        'deskripsi',
        'spesifikasi',
        'kategori',
        'estimasi_harga',
        'catatan',
        'status',
        'catatan_admin',
        'reviewed_by',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'estimasi_harga' => 'decimal:2',
            'reviewed_at'    => 'datetime',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(VendorSubmissionPhoto::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
