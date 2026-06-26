<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\VendorQualification;
use App\Models\VendorCertification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
class VendorQualificationController extends Controller {
    /** Vendor: lihat kualifikasi saya */
    public function show(): JsonResponse {
        $vendor = auth()->user()->vendor;
        if (!$vendor) return $this->error('Vendor tidak ditemukan.',null,404);
        return $this->success([
            'qualification'=>$vendor->qualification,
            'certifications'=>$vendor->certifications,
        ]);
    }
    /** Vendor: update kualifikasi */
    public function updateQualification(Request $req): JsonResponse {
        $vendor = auth()->user()->vendor;
        if (!$vendor) return $this->error('Vendor tidak ditemukan.',null,404);
        $req->validate(['kbli_code'=>'nullable|string|max:10','kbli_name'=>'nullable|string','business_scale'=>'nullable|in:kecil,menengah,besar','npwp'=>'nullable|string|max:30','siup_number'=>'nullable|string','tdp_number'=>'nullable|string','siup_expires_at'=>'nullable|date']);
        $q = VendorQualification::updateOrCreate(['vendor_id'=>$vendor->id],$req->only(['kbli_code','kbli_name','business_scale','npwp','siup_number','tdp_number','siup_expires_at']));
        return $this->success($q,'Kualifikasi berhasil diperbarui.');
    }
    /** Vendor: tambah sertifikasi */
    public function storeCertification(Request $req): JsonResponse {
        $vendor = auth()->user()->vendor;
        if (!$vendor) return $this->error('Vendor tidak ditemukan.',null,404);
        $req->validate(['name'=>'required|string','issuer'=>'required|string','certificate_number'=>'required|string','issued_at'=>'nullable|date','expires_at'=>'nullable|date']);
        $cert = $vendor->certifications()->create($req->only(['name','issuer','certificate_number','issued_at','expires_at']));
        return $this->success($cert,'Sertifikasi berhasil ditambahkan.',201);
    }
    /** Vendor: hapus sertifikasi */
    public function destroyCertification(int $id): JsonResponse {
        $vendor = auth()->user()->vendor;
        if (!$vendor) return $this->error('Vendor tidak ditemukan.',null,404);
        $cert = $vendor->certifications()->findOrFail($id);
        $cert->delete();
        return $this->success(null,'Sertifikasi dihapus.');
    }
}
