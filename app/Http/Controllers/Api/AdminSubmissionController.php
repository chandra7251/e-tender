<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\VendorSubmission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminSubmissionController extends Controller
{
    use ApiResponse;

    /**
     * GET /api/admin/submissions
     * Daftar semua pengajuan vendor. Bisa filter by status: ?status=pending|approved|rejected
     */
    public function index(Request $request): JsonResponse
    {
        $query = VendorSubmission::with(['vendor.user', 'photos', 'reviewer'])
            ->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $submissions = $query->get()->map(fn ($s) => $this->formatForAdmin($s));

        return $this->success($submissions, 'Daftar pengajuan vendor berhasil dimuat.');
    }

    /**
     * GET /api/admin/submissions/{id}
     * Detail pengajuan.
     */
    public function show(int $id): JsonResponse
    {
        $submission = VendorSubmission::with(['vendor.user', 'photos', 'reviewer'])
            ->findOrFail($id);

        return $this->success($this->formatForAdmin($submission));
    }

    /**
     * PATCH /api/admin/submissions/{id}/approve
     * Admin menyetujui pengajuan.
     */
    public function approve(int $id): JsonResponse
    {
        $submission = VendorSubmission::findOrFail($id);

        if ($submission->status !== 'pending') {
            return $this->error('Pengajuan sudah diproses sebelumnya.', null, 422);
        }

        $submission->update([
            'status'      => 'approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return $this->success(
            $this->formatForAdmin($submission->load(['vendor.user', 'photos', 'reviewer'])),
            'Pengajuan berhasil disetujui.'
        );
    }

    /**
     * PATCH /api/admin/submissions/{id}/reject
     * Admin menolak pengajuan. Wajib sertakan catatan_admin.
     */
    public function reject(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'catatan_admin' => 'required|string|min:10',
        ]);

        $submission = VendorSubmission::findOrFail($id);

        if ($submission->status !== 'pending') {
            return $this->error('Pengajuan sudah diproses sebelumnya.', null, 422);
        }

        $submission->update([
            'status'        => 'rejected',
            'catatan_admin' => $request->input('catatan_admin'),
            'reviewed_by'   => auth()->id(),
            'reviewed_at'   => now(),
        ]);

        return $this->success(
            $this->formatForAdmin($submission->load(['vendor.user', 'photos', 'reviewer'])),
            'Pengajuan berhasil ditolak.'
        );
    }

    // ─── Private Helpers ──────────────────────────────────────────────────────

    private function formatForAdmin(VendorSubmission $s): array
    {
        return [
            'id'             => $s->id,
            'vendor'         => [
                'id'           => $s->vendor?->id,
                'company_name' => $s->vendor?->company_name,
                'name'         => $s->vendor?->user?->name,
                'email'        => $s->vendor?->user?->email,
            ],
            'nama_barang'    => $s->nama_barang,
            'deskripsi'      => $s->deskripsi,
            'spesifikasi'    => $s->spesifikasi,
            'kategori'       => $s->kategori,
            'estimasi_harga' => $s->estimasi_harga,
            'catatan'        => $s->catatan,
            'status'         => $s->status,
            'catatan_admin'  => $s->catatan_admin,
            'reviewer'       => $s->reviewer ? [
                'id'   => $s->reviewer->id,
                'name' => $s->reviewer->name,
            ] : null,
            'reviewed_at'    => $s->reviewed_at?->toIso8601String(),
            'created_at'     => $s->created_at?->toIso8601String(),
            'photos'         => $s->photos->map(fn ($p) => [
                'id'        => $p->id,
                'photo_url' => $p->photo_url,
            ])->values(),
        ];
    }
}
