<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Tender;
use App\Models\TenderParticipant;
use App\Services\TenderHistoryService;
use Illuminate\Http\JsonResponse;
class TenderParticipantController extends Controller
{
    use ApiResponse;
    public function __construct(protected TenderHistoryService $historyService) {}
    public function check(Tender $tender): JsonResponse
    {
        $vendor = auth('api')->user()?->vendor;
        if (!$vendor) {
            return $this->success(['is_participant' => false, 'joined_at' => null]);
        }
        $participant = $tender->participants()
            ->where('vendor_id', $vendor->id)
            ->first();
        return $this->success([
            'is_participant' => !is_null($participant),
            'joined_at'      => $participant?->joined_at?->toIso8601String(),
        ]);
    }
    public function store(Tender $tender): JsonResponse
    {
        $user   = auth('api')->user();
        $vendor = $user?->vendor;
        if (!$vendor) {
            return $this->error('Profil vendor tidak ditemukan. Silakan hubungi admin.', null, 403);
        }
        if ($vendor->is_blacklisted) {
            return $this->error('Akun Anda diblokir (blacklist). Hubungi admin untuk informasi lebih lanjut.', null, 403);
        }
        if ($vendor->verification_status !== 'approved') {
            return $this->error(
                'Vendor belum diverifikasi. Tunggu persetujuan admin.',
                null, 403
            );
        }
        if (!in_array($tender->status, ['open', 'aanwijzing'])) {
            return $this->error(
                'Tender tidak dalam status yang dapat diikuti.',
                null, 422
            );
        }
        $alreadyJoined = TenderParticipant::withTrashed()
            ->where('tender_id', $tender->id)
            ->where('vendor_id', $vendor->id)
            ->exists();
        if ($alreadyJoined) {
            return $this->error('Vendor sudah pernah bergabung pada tender ini.', null, 422);
        }
        $participant = TenderParticipant::create([
            'tender_id' => $tender->id,
            'vendor_id' => $vendor->id,
            'joined_at' => now(),
        ]);
        $this->historyService->log(
            tenderId:    $tender->id,
            actorId:     $user->id,
            action:      'vendor_joined',
            oldStatus:   $tender->status,
            newStatus:   $tender->status,
            description: "{$vendor->company_name} bergabung pada tender."
        );
        return $this->created([
            'tender_id' => $tender->id,
            'joined_at' => $participant->joined_at->toIso8601String(),
        ], 'Berhasil bergabung pada tender.');
    }
}
