<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Services\BlockchainService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BlockchainController extends Controller
{
    public function __construct(private BlockchainService $blockchain) {}

    /** Rekam event tender ke blockchain */
    public function record(Request $request): JsonResponse
    {
        $request->validate([
            'event_type' => 'required|string',
            'tender_id'  => 'required|integer|exists:tenders,id',
            'data'       => 'required|array',
        ]);
        $record = $this->blockchain->recordTenderEvent($request->event_type, $request->tender_id, $request->data);
        return $this->success($record, 'Event berhasil direkam ke blockchain.');
    }

    /** Verifikasi integritas record */
    public function verify(int $recordId): JsonResponse
    {
        $result = $this->blockchain->verifyRecord($recordId);
        return $this->success($result);
    }

    /** Lihat chain lengkap untuk satu tender */
    public function tenderChain(int $tenderId): JsonResponse
    {
        $chain = $this->blockchain->getTenderChain($tenderId);
        return $this->success($chain);
    }

    /** Public verify via hash (tidak perlu auth) */
    public function publicVerify(Request $request): JsonResponse
    {
        $request->validate(['hash' => 'required|string|min:16']);
        $result = $this->blockchain->publicVerify($request->hash);
        return $this->success($result);
    }
}
