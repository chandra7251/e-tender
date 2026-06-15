<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\VendorDocumentRequest;
use App\Http\Traits\ApiResponse;
use App\Models\VendorDocument;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VendorDocumentController extends Controller
{
    use ApiResponse;

    // Fungsi buat ngelist semua file dokumen yang udah diupload si vendor
    public function index(): JsonResponse
    {
        $vendor    = auth()->user()->vendor;
        $documents = $vendor->documents()->orderByDesc('uploaded_at')->get()
            ->map(fn ($d) => [
                'id'            => $d->id,
                'document_type' => $d->document_type,
                'file_name'     => $d->file_name,
                'mime_type'     => $d->mime_type,
                'file_size'     => $d->file_size,
                'uploaded_at'   => $d->uploaded_at?->toIso8601String(),
            ]);

        return $this->success($documents);
    }

    // Fungsi buat ngupload file dokumen baru (misal KTP, NPWP, dll)
    public function store(VendorDocumentRequest $request): JsonResponse
    {
        $vendor = auth()->user()->vendor;
        $file   = $request->file('file');

        // Simpen filenya di local storage server ya, jangan dilempar ke S3 dulu kalo belum perlu
        $hashedName = $file->hashName();
        $path       = $file->storeAs("vendor-documents/{$vendor->id}", $hashedName, 'local');

        $document = VendorDocument::create([
            'vendor_id'     => $vendor->id,
            'document_type' => $request->input('document_type'),
            'file_name'     => $file->getClientOriginalName(),
            'file_path'     => $path,
            'mime_type'     => $file->getMimeType(),
            'file_size'     => $file->getSize(),
            'uploaded_at'   => now(),
        ]);

        return $this->created([
            'id'            => $document->id,
            'document_type' => $document->document_type,
            'file_name'     => $document->file_name,
            'file_size'     => $document->file_size,
            'uploaded_at'   => $document->uploaded_at?->toIso8601String(),
        ], 'Dokumen berhasil diupload.');
    }

    // Fungsi buat vendor kalo mau ngedownload dokumen yang pernah dia upload sendiri
    public function download(VendorDocument $document): StreamedResponse|JsonResponse
    {
        $vendor = auth()->user()->vendor;

        // Pastiin dokumennya emang beneran punya dia, ga boleh nyomot punya vendor sebelah
        if ($document->vendor_id !== $vendor->id) {
            return $this->error('Dokumen tidak ditemukan.', null, 404);
        }

        if (!Storage::disk('local')->exists($document->file_path)) {
            return $this->error('File tidak ditemukan di server.', null, 404);
        }

        return Storage::disk('local')->download(
            $document->file_path,
            $document->file_name
        );
    }
}
