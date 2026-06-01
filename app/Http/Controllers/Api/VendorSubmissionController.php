<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\VendorSubmission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VendorSubmissionController extends Controller
{
    use ApiResponse;

    /**
     * GET /api/vendor/submissions
     * Riwayat pengajuan milik vendor yang sedang login.
     */
    public function index(): JsonResponse
    {
        $vendor = auth()->user()->vendor;

        $submissions = VendorSubmission::with('photos')
            ->where('vendor_id', $vendor->id)
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($s) => $this->formatSubmission($s));

        return $this->success($submissions, 'Riwayat pengajuan berhasil dimuat.');
    }

    /**
     * GET /api/vendor/submissions/{id}
     * Detail pengajuan milik vendor yang sedang login.
     */
    public function show(int $id): JsonResponse
    {
        $vendor     = auth()->user()->vendor;
        $submission = VendorSubmission::with('photos')
            ->where('vendor_id', $vendor->id)
            ->findOrFail($id);

        return $this->success($this->formatSubmission($submission));
    }

    /**
     * POST /api/vendor/submissions
     * Buat pengajuan baru. Hanya vendor approved yang bisa submit.
     * Foto (opsional) dikirim sebagai multipart/form-data photos[].
     */
    public function store(Request $request): JsonResponse
    {
        // Guard: hanya vendor approved
        $vendor = auth()->user()->vendor;
        if ($vendor->verification_status !== 'approved') {
            return $this->error(
                'Akun Anda belum diverifikasi. Hanya vendor yang sudah disetujui dapat mengajukan tender.',
                null,
                403
            );
        }

        $validated = $request->validate([
            'nama_barang'    => 'required|string|max:255',
            'deskripsi'      => 'required|string',
            'spesifikasi'    => 'nullable|string',
            'kategori'       => 'nullable|string|max:100',
            'estimasi_harga' => 'nullable|numeric|min:0',
            'catatan'        => 'nullable|string',
            'photos'         => 'nullable|array|max:3',
            'photos.*'       => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        // Simpan submission
        $submission = VendorSubmission::create([
            'vendor_id'      => $vendor->id,
            'nama_barang'    => $validated['nama_barang'],
            'deskripsi'      => $validated['deskripsi'],
            'spesifikasi'    => $validated['spesifikasi'] ?? null,
            'kategori'       => $validated['kategori'] ?? null,
            'estimasi_harga' => $validated['estimasi_harga'] ?? null,
            'catatan'        => $validated['catatan'] ?? null,
            'status'         => 'pending',
        ]);

        // Handle upload foto (opsional)
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $filename = $photo->hashName();
                $path     = $photo->storeAs('submission-photos', $filename, 'public');
                $url      = Storage::disk('public')->url($path);

                $submission->photos()->create([
                    'photo_path' => $path,
                    'photo_url'  => $url,
                ]);
            }
        }

        return $this->created(
            $this->formatSubmission($submission->load('photos')),
            'Pengajuan tender berhasil dikirim.'
        );
    }

    // ─── Private Helpers ──────────────────────────────────────────────────────

    private function formatSubmission(VendorSubmission $s): array
    {
        return [
            'id'             => $s->id,
            'nama_barang'    => $s->nama_barang,
            'deskripsi'      => $s->deskripsi,
            'spesifikasi'    => $s->spesifikasi,
            'kategori'       => $s->kategori,
            'estimasi_harga' => $s->estimasi_harga,
            'catatan'        => $s->catatan,
            'status'         => $s->status,
            'catatan_admin'  => $s->catatan_admin,
            'reviewed_at'    => $s->reviewed_at?->toIso8601String(),
            'created_at'     => $s->created_at?->toIso8601String(),
            'photos'         => $s->photos->map(fn ($p) => [
                'id'        => $p->id,
                'photo_url' => $p->photo_url,
            ])->values(),
        ];
    }
}
