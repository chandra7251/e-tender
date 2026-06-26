<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorCataloguePhoto extends Model
{
    protected $fillable = ['catalogue_item_id','photo_path','is_primary'];
    protected $casts    = ['is_primary' => 'boolean'];

    public function item(): BelongsTo
    {
        return $this->belongsTo(VendorCatalogueItem::class, 'catalogue_item_id');
    }

    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->photo_path);
    }
}
