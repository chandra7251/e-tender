<?php
namespace App\Http\Controllers\Api;

use App\Models\CatalogueCategory;
use App\Models\VendorCatalogueItem;
use App\Models\VendorCataloguePhoto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VendorCatalogueController extends BaseApiController
{
    /** GET /api/catalogue — publik, semua item aktif */
    public function index(Request $request): JsonResponse
    {
        $q = VendorCatalogueItem::with(['vendor','category','photos'])
            ->where('is_active', true);

        if ($request->filled('category')) {
            $q->where('category_id', $request->category);
        }
        if ($request->filled('search')) {
            $q->where('name', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('vendor_id')) {
            $q->where('vendor_id', $request->vendor_id);
        }

        $items = $q->latest()->paginate(20);

        return $this->success($items);
    }

    /** GET /api/catalogue/categories */
    public function categories(): JsonResponse
    {
        $cats = CatalogueCategory::withCount('items')->get();
        return $this->success($cats);
    }

    /** GET /api/catalogue/{id} — detail item */
    public function show(int $id): JsonResponse
    {
        $item = VendorCatalogueItem::with(['vendor.user','category','photos'])
            ->where('is_active', true)->findOrFail($id);
        return $this->success($item);
    }

    /** GET /api/vendor/catalogue — katalog milik vendor yg login */
    public function myItems(): JsonResponse
    {
        $vendor = auth('api')->user()->vendor;
        if (!$vendor) return $this->error('Vendor tidak ditemukan.', 404);

        $items = VendorCatalogueItem::with(['category','photos'])
            ->where('vendor_id', $vendor->id)->latest()->get();
        return $this->success($items);
    }

    /** POST /api/vendor/catalogue */
    public function store(Request $request): JsonResponse
    {
        $vendor = auth('api')->user()->vendor;
        if (!$vendor) return $this->error('Vendor tidak ditemukan.', 404);

        $data = $request->validate([
            'name'          => 'required|string|max:200',
            'description'   => 'nullable|string|max:2000',
            'category_id'   => 'nullable|exists:catalogue_categories,id',
            'price_estimate'=> 'nullable|numeric|min:0',
            'unit'          => 'nullable|string|max:50',
            'specs'         => 'nullable|array',
            'photos'        => 'nullable|array|max:5',
            'photos.*'      => 'image|max:2048',
        ]);

        $item = VendorCatalogueItem::create([
            'vendor_id'     => $vendor->id,
            'category_id'   => $data['category_id'] ?? null,
            'name'          => $data['name'],
            'description'   => $data['description'] ?? null,
            'price_estimate'=> $data['price_estimate'] ?? null,
            'unit'          => $data['unit'] ?? 'unit',
            'specs'         => $data['specs'] ?? null,
            'is_active'     => true,
        ]);

        // Upload photos
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $i => $photo) {
                $path = $photo->store('catalogue', 'public');
                VendorCataloguePhoto::create([
                    'catalogue_item_id' => $item->id,
                    'photo_path'        => $path,
                    'is_primary'        => $i === 0,
                ]);
            }
        }

        return $this->success($item->load(['category','photos']), 'Item katalog berhasil ditambahkan.', 201);
    }

    /** PUT /api/vendor/catalogue/{id} */
    public function update(Request $request, int $id): JsonResponse
    {
        $vendor = auth('api')->user()->vendor;
        $item = VendorCatalogueItem::where('vendor_id', $vendor->id)->findOrFail($id);

        $data = $request->validate([
            'name'          => 'sometimes|string|max:200',
            'description'   => 'nullable|string|max:2000',
            'category_id'   => 'nullable|exists:catalogue_categories,id',
            'price_estimate'=> 'nullable|numeric|min:0',
            'unit'          => 'nullable|string|max:50',
            'specs'         => 'nullable|array',
            'is_active'     => 'boolean',
        ]);

        $item->update($data);
        return $this->success($item->load(['category','photos']), 'Item katalog berhasil diupdate.');
    }

    /** DELETE /api/vendor/catalogue/{id} */
    public function destroy(int $id): JsonResponse
    {
        $vendor = auth('api')->user()->vendor;
        $item = VendorCatalogueItem::where('vendor_id', $vendor->id)->findOrFail($id);
        $item->photos->each(fn($p) => Storage::disk('public')->delete($p->photo_path));
        $item->delete();
        return $this->success(null, 'Item katalog berhasil dihapus.');
    }
}
