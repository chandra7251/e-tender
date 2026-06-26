<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VendorCatalogueItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'vendor_id','category_id','name','description',
        'price_estimate','unit','specs','is_active',
    ];

    protected $casts = [
        'specs'      => 'array',
        'is_active'  => 'boolean',
        'price_estimate' => 'decimal:2',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(CatalogueCategory::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(VendorCataloguePhoto::class, 'catalogue_item_id');
    }

    public function primaryPhoto(): ?VendorCataloguePhoto
    {
        return $this->photos()->where('is_primary', true)->first()
            ?? $this->photos()->first();
    }
}
