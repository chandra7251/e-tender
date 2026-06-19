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

    // Tampilkan profil lengkap vendor yang sedang login, termasuk data user
    public function show(): JsonResponse
    {
        // Eager-load relasi 'user' sekaligus agar tidak ada lazy-load di Resource
        $vendor = auth('api')->user()?->vendor()->with('user')->first();

        if (!$vendor) {
            // User ada tapi vendor record tidak ada — kemungkinan registrasi tidak selesai
            return $this->error('Profil vendor tidak ditemukan. Silakan hubungi admin.', null, 404);
        }

        return $this->success(new VendorResource($vendor));
    }

    // Perbarui data profil vendor (nama, nama perusahaan, telepon, alamat)
    public function update(VendorProfileRequest $request): JsonResponse
    {
        $user   = auth('api')->user();
        $vendor = $user?->vendor;

        if (!$vendor) {
            return $this->error('Profil vendor tidak ditemukan. Silakan hubungi admin.', null, 404);
        }

        // Update nama di tabel 'users' jika dikirim dalam request
        if ($request->filled('name')) {
            $user->update(['name' => $request->input('name')]);
        }

        // Update data bisnis vendor di tabel 'vendors'
        $vendor->update([
            'company_name' => $request->input('company_name'),
            'phone'        => $request->input('phone'),
            'address'      => $request->input('address'),
        ]);

        // Refresh relasi user agar response menampilkan nama terbaru
        return $this->success(
            new VendorResource($vendor->load('user')),
            'Profil berhasil diperbarui.'
        );
    }

    // Cek status verifikasi vendor tanpa harus load seluruh profil
    // Digunakan untuk polling status dari halaman waiting screen
    public function status(): JsonResponse
    {
        $vendor = auth('api')->user()?->vendor;

        if (!$vendor) {
            // Return data null daripada error agar frontend bisa handle gracefully
            return $this->success([
                'verification_status' => null,
                'verification_notes'  => null,
                'verified_at'         => null,
            ]);
        }

        return $this->success([
            'verification_status' => $vendor->verification_status,
            'verification_notes'  => $vendor->verification_notes,
            'verified_at'         => $vendor->verified_at?->toIso8601String(),
        ]);
    }
}
