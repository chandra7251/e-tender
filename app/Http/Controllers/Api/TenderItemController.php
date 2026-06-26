<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Tender;
use App\Models\TenderItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
class TenderItemController extends Controller {
    /** GET - list items BQ untuk tender */
    public function index(Tender $tender): JsonResponse {
        return $this->success($tender->items()->orderBy('sort_order')->get());
    }
    /** Admin: simpan BQ sekaligus (replace) */
    public function sync(Request $req, Tender $tender): JsonResponse {
        $req->validate(['items'=>'required|array|min:1','items.*.description'=>'required|string','items.*.unit'=>'required|string','items.*.quantity'=>'required|numeric|min:0.0001','items.*.hps_unit_price'=>'required|numeric|min:0']);
        $tender->items()->delete();
        $items = collect($req->items)->map(fn($it,$i)=>array_merge($it,['tender_id'=>$tender->id,'sort_order'=>$i,'created_at'=>now(),'updated_at'=>now()]));
        TenderItem::insert($items->toArray());
        return $this->success($tender->items()->orderBy('sort_order')->get(),'BQ berhasil disimpan.');
    }
    /** Admin: hapus satu item */
    public function destroy(Tender $tender, TenderItem $item): JsonResponse {
        if ($item->tender_id !== $tender->id) abort(404);
        $item->delete();
        return $this->success(null,'Item dihapus.');
    }
}
