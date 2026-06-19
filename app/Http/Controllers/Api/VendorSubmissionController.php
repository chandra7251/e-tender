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

    // Helper privat: ambil vendor dari user yang sedang login
    private function resolveVendor(): ?\App\Models\Vendor
    {
        return auth('api')->user()?->vendor;
    }

    // Tampilkan semua histori pengajuan barang milik vendor yang login
    public function index(): JsonResponse
    {
        // Null guard: user terdaftar tapi mungkin tidak punya vendor record
        $vendor = $this->resolveVendor();
        if (!$vendor) {
            return $this->error('Profil vendor tidak ditemukan.', null, 404);
        }

        // Ambil pengajuan beserta foto, diurutkan dari terbaru
        $submissions = VendorSubmission::with('photos')
            ->where('vendor_id', $vendor->id)
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($s) => $this->formatSubmission($s));

        return $this->success($submissions, 'Riwayat pengajuan berhasil dimuat.');
    }

    // Tampilkan detail satu pengajuan tertentu milik vendor yang login
    public function show(int $id): JsonResponse
    {
        // Null guard
        $vendor = $this->resolveVendor();
        if (!$vendor) {
            return $this->error('Profil vendor tidak ditemukan.', null, 404);
        }

        // findOrFail otomatis return 404 jika tidak ditemukan
        // Scope vendor_id memastikan vendor hanya bisa akses pengajuan miliknya sendiri
        $submission = VendorSubmission::with('photos')
            ->where('vendor_id', $vendor->id)
            ->findOrFail($id);

        return $this->success($this->formatSubmission($submission));
    }

    // Kirim pengajuan barang baru beserta foto (maks 3 foto)
    public function store(Request $request): JsonResponse
    {
        // Null guard
        $vendor = $this->resolveVendor();
        if (!$vendor) {
            return $this->error('Profil vendor tidak ditemukan.', null, 404);
        }

        // Hanya vendor yang sudah disetujui admin yang boleh mengajukan
        if ($vendor->verification_status !== 'approved') {
            return $this->error(
                'Akun Anda belum diverifikasi. Hanya vendor yang sudah disetujui dapat mengajukan tender.',
                null,
                403
            );
        }

        // Validasi input dari form pengajuan
        $validated = $request->validate([
            'nama_barang'    => 'required|string|max:255',
            'deskripsi'      => 'required|string',
            'spesifikasi'    => 'nullable|string',
            'kategori'       => 'nullable|string|max:100',
            'estimasi_harga' => 'nullable|numeric|min:0',
            'catatan'        => 'nullable|string',
            'photos'         => 'nullable|array|max:3',
            // Validasi MIME dan ukuran file di sisi server
            'photos.*'       => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        // Simpan data pengajuan dengan status awal 'pending' (menunggu review admin)
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

        // Simpan setiap foto ke disk public dan catat path-nya di database
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $filename = $photo->hashName(); // Nama file di-hash untuk menghindari collision
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

    // Helper privat: format data submission ke bentuk array yang konsisten untuk response
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
