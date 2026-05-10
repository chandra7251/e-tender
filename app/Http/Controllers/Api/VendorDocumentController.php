<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\VendorDocumentRequest;
use App\Http\Traits\ApiResponse;
use App\Models\VendorDocument;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class VendorDocumentController extends Controller
{
    use ApiResponse;

    /** GET /api/vendors/documents */
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

    /** POST /api/vendors/documents */
    public function store(VendorDocumentRequest $request): JsonResponse
    {
        $vendor = auth()->user()->vendor;
        $file   = $request->file('file');

        $path = $file->store("vendor-documents/{$vendor->id}", 'public');

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
}
