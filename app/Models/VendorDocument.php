<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
class VendorDocument extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'vendor_id',
        'document_type',
        'file_name',
        'file_path',
        'mime_type',
        'file_size',
        'uploaded_at',
    ];
    protected function casts(): array
    {
        return [
            'uploaded_at' => 'datetime',
            'file_size'   => 'integer',
        ];
    }
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }
}
