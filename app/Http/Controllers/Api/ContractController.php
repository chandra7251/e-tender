<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Tender;
use App\Models\TenderResult;
use App\Services\FcmService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    public function __construct(private FcmService $fcm) {}

    /** Admin: list semua kontrak */
    public function index(Request $req): JsonResponse
    {
        $q = Contract::with(['tender:id,title','vendor.user:id,name','creator:id,name'])->latest();
        if ($req->status) $q->where('status', $req->status);
        return $this->success($q->paginate(20));
    }

    /** Admin: buat kontrak dari hasil tender */
    public function store(Request $req): JsonResponse
    {
        $req->validate([
            'tender_id'      => 'required|exists:tenders,id',
            'notes'          => 'nullable|string',
            'contract_value' => 'required|numeric|min:0',
        ]);
        $tender  = Tender::findOrFail($req->tender_id);
        $result  = TenderResult::where('tender_id', $tender->id)->first();
        if (!$result || !$result->winner_vendor_id)
            return $this->error('Pemenang tender belum ditentukan.', null, 422);
        $existing = Contract::where('tender_id', $tender->id)->whereNotIn('status', ['terminated'])->first();
        if ($existing)
            return $this->error('Kontrak untuk tender ini sudah ada.', null, 422);
        $seq = str_pad(Contract::whereYear('created_at', now()->year)->count() + 1, 3, '0', STR_PAD_LEFT);
        $contractNumber = 'KONTRAK-ZETA/' . now()->format('m') . '/' . now()->format('Y') . '/' . $seq;
        $contract = Contract::create([
            'tender_id'       => $tender->id,
            'vendor_id'       => $result->winner_vendor_id,
            'created_by'      => auth('api')->id(),
            'contract_number' => $contractNumber,
            'contract_value'  => $req->contract_value,
            'notes'           => $req->notes,
            'status'          => 'draft',
        ]);
        return $this->success($contract->load(['tender:id,title', 'vendor.user:id,name']), 'Kontrak berhasil dibuat.', 201);
    }

    /** Admin: kirim kontrak ke vendor */
    public function sendToVendor(Contract $contract): JsonResponse
    {
        if ($contract->status !== 'draft')
            return $this->error('Kontrak tidak dalam status draft.', null, 422);
        $contract->update(['status' => 'sent_to_vendor']);

        // Push notification ke vendor
        $vendorUser = $contract->vendor->user ?? null;
        if ($vendorUser) {
            $this->fcm->notifyUser(
                $vendorUser,
                '\uD83D\uDCC4 Kontrak Baru Menunggu TTD Anda',
                'Kontrak ' . $contract->contract_number . ' telah dikirim. Segera tandatangani.',
                ['type' => 'contract_sent', 'contract_id' => (string) $contract->id]
            );
        }
        return $this->success($contract, 'Kontrak berhasil dikirim ke vendor.');
    }

    /** Vendor: list kontrak milik vendor */
    public function vendorContracts(): JsonResponse
    {
        $vendor = auth('api')->user()->vendor;
        if (!$vendor) return $this->error('Vendor tidak ditemukan.', null, 404);
        $contracts = Contract::with(['tender:id,title', 'deliveries'])
            ->where('vendor_id', $vendor->id)->latest()->get();
        return $this->success($contracts);
    }

    /** Vendor/Admin: detail kontrak */
    public function show(Contract $contract): JsonResponse
    {
        $vendor = auth('api')->user()->vendor;
        if ($vendor && $contract->vendor_id !== $vendor->id)
            return $this->error('Tidak diizinkan.', null, 403);
        return $this->success($contract->load(['tender:id,title', 'vendor.user:id,name', 'deliveries', 'creator:id,name']));
    }

    /** Vendor: tanda tangan kontrak */
    public function vendorSign(Contract $contract): JsonResponse
    {
        $vendor = auth('api')->user()->vendor;
        if (!$vendor || $contract->vendor_id !== $vendor->id)
            return $this->error('Tidak diizinkan.', null, 403);
        if ($contract->status !== 'sent_to_vendor')
            return $this->error('Kontrak belum dikirim ke vendor.', null, 422);
        $contract->update(['status' => 'signed_vendor', 'vendor_signed_at' => now()]);

        // Push notif ke semua admin
        $admins = \App\Models\User::whereIn('role', ['admin', 'super_admin', 'procurement_manager'])
            ->whereNotNull('fcm_token')->get();
        $tokens = $admins->pluck('fcm_token')->filter()->values()->toArray();
        if (!empty($tokens)) {
            $this->fcm->sendToMultiple(
                $tokens,
                '\u270D\uFE0F Vendor Menandatangani Kontrak',
                'Kontrak ' . $contract->contract_number . ' telah ditandatangani vendor. Menunggu TTD Admin.',
                ['type' => 'contract_signed_vendor', 'contract_id' => (string) $contract->id]
            );
        }
        return $this->success($contract, 'Kontrak berhasil ditandatangani.');
    }

    /** Admin: aktifkan kontrak */
    public function adminSign(Contract $contract): JsonResponse
    {
        if ($contract->status !== 'signed_vendor')
            return $this->error('Vendor belum menandatangani kontrak.', null, 422);
        $hash = hash('sha256', $contract->contract_number . $contract->contract_value . $contract->vendor_id);
        $contract->update(['status' => 'active', 'admin_signed_at' => now(), 'document_hash' => $hash]);

        // Push notif ke vendor
        $vendorUser = $contract->vendor->user ?? null;
        if ($vendorUser) {
            $this->fcm->notifyUser(
                $vendorUser,
                '\u2705 Kontrak Aktif!',
                'Kontrak ' . $contract->contract_number . ' kini AKTIF. Silakan mulai proses pengiriman.',
                ['type' => 'contract_active', 'contract_id' => (string) $contract->id]
            );
        }
        return $this->success($contract, 'Kontrak diaktifkan.');
    }

    /** Admin: selesaikan kontrak */
    public function complete(Contract $contract): JsonResponse
    {
        if ($contract->status !== 'active')
            return $this->error('Kontrak belum aktif.', null, 422);
        $contract->update(['status' => 'completed', 'completed_at' => now()]);

        // Push notif ke vendor
        $vendorUser = $contract->vendor->user ?? null;
        if ($vendorUser) {
            $this->fcm->notifyUser(
                $vendorUser,
                '\uD83C\uDF89 Kontrak Selesai!',
                'Kontrak ' . $contract->contract_number . ' telah selesai. Terima kasih!',
                ['type' => 'contract_completed', 'contract_id' => (string) $contract->id]
            );
        }
        return $this->success($contract, 'Kontrak berhasil diselesaikan.');
    }
}
