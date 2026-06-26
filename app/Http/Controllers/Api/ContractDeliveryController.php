<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\ContractDelivery;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
class ContractDeliveryController extends Controller {
    /** Vendor: upload bukti progress */
    public function vendorUpdate(Request $req, Contract $contract, ContractDelivery $delivery): JsonResponse {
        $vendor = auth()->user()->vendor;
        if (!$vendor || $contract->vendor_id !== $vendor->id) return $this->error('Tidak diizinkan.',null,403);
        if (!in_array($delivery->status,['scheduled','in_progress'])) return $this->error('Milestone sudah selesai.',null,422);
        $req->validate(['vendor_notes'=>'nullable|string','evidence'=>'nullable|file|max:10240']);
        $data = ['status'=>'in_progress','vendor_notes'=>$req->vendor_notes];
        if ($req->hasFile('evidence')) {
            $path = $req->file('evidence')->store("contracts/{$contract->id}/evidence",'public');
            $data['evidence_path'] = $path;
            $data['delivered_at'] = now();
            $data['status'] = 'delivered';
        }
        $delivery->update($data);
        return $this->success($delivery,'Progress berhasil diperbarui.');
    }
    /** Admin: verifikasi milestone */
    public function adminVerify(Contract $contract, ContractDelivery $delivery): JsonResponse {
        if ($delivery->status !== 'delivered') return $this->error('Milestone belum diserahkan vendor.',null,422);
        $delivery->update(['status'=>'verified','verified_at'=>now(),'verified_by'=>auth()->id()]);
        // Cek apakah semua milestone selesai
        $allDone = $contract->deliveries()->whereNotIn('status',['verified'])->doesntExist();
        if ($allDone) $contract->update(['status'=>'completed']);
        return $this->success($delivery,'Milestone berhasil diverifikasi.');
    }
    /** Admin: tambah milestone */
    public function store(Request $req, Contract $contract): JsonResponse {
        $req->validate(['milestone_name'=>'required|string','description'=>'nullable|string','due_date'=>'required|date']);
        $d = $contract->deliveries()->create($req->only(['milestone_name','description','due_date']));
        return $this->success($d,'Milestone ditambahkan.',201);
    }
}
