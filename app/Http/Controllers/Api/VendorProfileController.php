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

    /** GET /api/vendors/me */
    public function show(): JsonResponse
    {
        $vendor = auth()->user()->vendor()->with('user')->first();
        return $this->success(new VendorResource($vendor));
    }

    /** PUT /api/vendors/me */
    public function update(VendorProfileRequest $request): JsonResponse
    {
        $vendor = auth()->user()->vendor;

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

    /** GET /api/vendors/status */
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
