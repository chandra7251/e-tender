<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CatalogueCategory extends Model
{
    protected $fillable = ['name','slug','parent_id'];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(CatalogueCategory::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(CatalogueCategory::class, 'parent_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(VendorCatalogueItem::class, 'category_id');
    }
}
