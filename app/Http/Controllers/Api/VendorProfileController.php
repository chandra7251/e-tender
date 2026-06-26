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
    public function show(): JsonResponse
    {
        $vendor = auth('api')->user()?->vendor()->with('user')->first();
        if (!$vendor) {
            return $this->error('Profil vendor tidak ditemukan. Silakan hubungi admin.', null, 404);
        }
        return $this->success(new VendorResource($vendor));
    }
    public function update(VendorProfileRequest $request): JsonResponse
    {
        $user   = auth('api')->user();
        $vendor = $user?->vendor;
        if (!$vendor) {
            return $this->error('Profil vendor tidak ditemukan. Silakan hubungi admin.', null, 404);
        }
        if ($request->filled('name')) {
            $user->update(['name' => $request->input('name')]);
        }
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
    public function status(): JsonResponse
    {
        $vendor = auth('api')->user()?->vendor;
        if (!$vendor) {
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

    /**
     * Get vendor's own rating summary (for mobile profile page).
     */
    public function myRating(): \Illuminate\Http\JsonResponse
    {
        $vendor = auth('api')->user()?->vendor;
        if (!$vendor) {
            return $this->error('Profil vendor tidak ditemukan.', null, 403);
        }

        $ratings = \App\Models\VendorRating::where('vendor_id', $vendor->id)
            ->with('tender:id,title')
            ->latest()
            ->get();

        $avg = $ratings->avg('overall_score');

        return $this->success([
            'average_rating'   => $avg ? round((float)$avg, 2) : null,
            'total_ratings'    => $ratings->count(),
            'is_blacklisted'   => (bool) $vendor->is_blacklisted,
            'blacklist_reason' => $vendor->is_blacklisted ? $vendor->blacklist_reason : null,
            'ratings'          => $ratings->map(fn($r) => [
                'tender_title'       => $r->tender?->title,
                'overall_score'      => $r->overall_score,
                'quality_score'      => $r->quality_score,
                'delivery_score'     => $r->delivery_score,
                'communication_score'=> $r->communication_score,
                'compliance_score'   => $r->compliance_score,
                'review'             => $r->review,
                'rated_at'           => $r->created_at?->toIso8601String(),
            ])->values(),
        ]);
    }

}
