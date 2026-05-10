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

    /** POST /api/tenders/{tender}/participants */
    public function store(Tender $tender): JsonResponse
    {
        $user   = auth()->user();
        $vendor = $user->vendor;

        // Guard: only approved vendors can join
        if ($vendor->verification_status !== 'approved') {
            return $this->error(
                'Vendor belum diverifikasi. Tunggu persetujuan admin.',
                null, 403
            );
        }

        // Guard: tender must be joinable
        if (!in_array($tender->status, ['open', 'aanwijzing'])) {
            return $this->error(
                'Tender tidak dalam status yang dapat diikuti.',
                null, 422
            );
        }

        // Guard: no duplicate join
        $alreadyJoined = TenderParticipant::where('tender_id', $tender->id)
            ->where('vendor_id', $vendor->id)
            ->exists();

        if ($alreadyJoined) {
            return $this->error('Vendor sudah bergabung pada tender ini.', null, 422);
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
