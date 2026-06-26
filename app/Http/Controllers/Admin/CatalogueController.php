<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CatalogueCategory;
use App\Models\VendorCatalogueItem;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogueController extends Controller
{
    public function index(Request $request): View
    {
        $q = VendorCatalogueItem::with(['vendor','category','photos']);

        if ($request->filled('category')) $q->where('category_id', $request->category);
        if ($request->filled('search'))   $q->where('name', 'like', '%'.$request->search.'%');
        if ($request->filled('status'))   $q->where('is_active', $request->status === 'active');

        $items      = $q->latest()->paginate(24);
        $categories = CatalogueCategory::all();
        $totalItems = VendorCatalogueItem::count();
        $activeItems= VendorCatalogueItem::where('is_active', true)->count();

        return view('admin.catalogue.index', compact('items','categories','totalItems','activeItems'));
    }

    public function show(int $id): View
    {
        $item = VendorCatalogueItem::with(['vendor.user','category','photos'])->findOrFail($id);
        return view('admin.catalogue.show', compact('item'));
    }

    public function toggleActive(int $id)
    {
        $item = VendorCatalogueItem::findOrFail($id);
        $item->update(['is_active' => !$item->is_active]);
        return back()->with('success', 'Status item katalog diperbarui.');
    }
}
