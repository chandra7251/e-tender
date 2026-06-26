<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\TenderComplaint;
use App\Models\Tender;
use App\Models\InstansiSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
class TenderComplaintController extends Controller {
    /** Vendor: list sanggahan milik saya */
    public function index(Request $req): JsonResponse {
        $vendor = auth()->user()->vendor;
        if (!$vendor) return $this->error('Vendor tidak ditemukan.',null,404);
        $complaints = TenderComplaint::with(['tender:id,title'])
            ->where('vendor_id',$vendor->id)->latest()->get();
        return $this->success($complaints);
    }
    /** Vendor: ajukan sanggahan/banding */
    public function store(Request $req, Tender $tender): JsonResponse {
        $vendor = auth()->user()->vendor;
        if (!$vendor) return $this->error('Vendor tidak ditemukan.',null,404);
        // Cek tender result sudah ada
        if (!$tender->result) return $this->error('Hasil tender belum diumumkan.',null,422);
        $windowDays = (int) InstansiSetting::get('sanggahan_window_days', 5);
        $deadline = $tender->result->created_at->addWeekdays($windowDays);
        if (now()->gt($deadline)) return $this->error("Batas waktu sanggahan ({$windowDays} hari kerja) sudah berakhir.",null,422);
        // Cek vendor ikut tender
        $participated = $tender->participants()->where('vendor_id',$vendor->id)->exists();
        if (!$participated) return $this->error('Anda tidak terdaftar sebagai peserta tender ini.',null,403);
        // Cek tidak ada sanggahan pending
        $existing = TenderComplaint::where('tender_id',$tender->id)->where('vendor_id',$vendor->id)->where('type','sanggahan')->where('status','pending')->first();
        if ($existing) return $this->error('Anda sudah memiliki sanggahan yang sedang diproses.',null,422);
        $req->validate(['type'=>'required|in:sanggahan,banding','reason'=>'required|string|min:30|max:2000']);
        $complaint = TenderComplaint::create([
            'tender_id'=>$tender->id,'vendor_id'=>$vendor->id,
            'type'=>$req->type,'reason'=>$req->reason,'deadline'=>$deadline,
        ]);
        return $this->success($complaint,'Sanggahan berhasil diajukan.',201);
    }
    /** Admin: list semua sanggahan */
    public function adminIndex(Request $req): JsonResponse {
        $q = TenderComplaint::with(['tender:id,title','vendor.user:id,name,email'])->latest();
        if ($req->status) $q->where('status',$req->status);
        if ($req->type) $q->where('type',$req->type);
        return $this->success($q->paginate(20));
    }
    /** Admin: respons sanggahan */
    public function respond(Request $req, TenderComplaint $complaint): JsonResponse {
        $req->validate(['status'=>'required|in:accepted,rejected','response'=>'required|string|min:10']);
        $complaint->update(['status'=>$req->status,'response'=>$req->response,'responded_by'=>auth()->id(),'responded_at'=>now()]);
        return $this->success($complaint,'Sanggahan berhasil direspons.');
    }
}
