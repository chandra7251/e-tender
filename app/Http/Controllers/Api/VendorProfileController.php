<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\VendorProfileRequest;
use App\Http\Resources\VendorResource;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class VendorProfileController extends Controller
{
    use ApiResponse;

    // Fungsi ngambil data profil si vendor yang lagi login
    public function show(): JsonResponse
    {
        $vendor = auth()->user()->vendor()->with('user')->first();
        return $this->success(new VendorResource($vendor));
    }

    // Fungsi buat ngupdate data profilnya dia
    public function update(VendorProfileRequest $request): JsonResponse
    {
        $user   = auth()->user();
        $vendor = $user->vendor;

        // Kalo dia ngubah nama, kita update di tabel users
        if ($request->filled('name')) {
            $user->update(['name' => $request->input('name')]);
        }

        // Sisanya (nama perusahaan, hp, alamat) kita update di tabel vendors
        $vendor->update([
            'company_name' => $request->input('company_name'),
            'phone'        => $request->input('phone'),
            'address'      => $request->input('address'),
        ]);

        return $this->success(
            new VendorResource($vendor->load('user')),
            'Profil berhasil diperbarui.'
        );
    }

    // Fungsi ini khusus buat ngecek status verifikasinya si vendor doang
    public function status(): JsonResponse
    {
        $vendor = auth()->user()->vendor;

        return $this->success([
            'verification_status' => $vendor->verification_status,
            'verification_notes'  => $vendor->verification_notes,
            'verified_at'         => $vendor->verified_at?->toIso8601String(),
        ]);
    }
}
