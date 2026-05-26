<?php

namespace App\Http\Controllers\Admin;

use App\Models\Vendor;
use App\Models\VendorDocument;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Http\RedirectResponse;

class VendorDocumentController extends \App\Http\Controllers\Controller
{
    /**
     * Download dokumen vendor untuk keperluan validasi admin.
     *
     * Route: GET /admin/vendors/{vendor}/documents/{document}/download
     * Middleware: auth + role:admin (dari group di web.php)
     */
    public function download(Vendor $vendor, VendorDocument $document): StreamedResponse|RedirectResponse
    {
        // Guard: pastikan dokumen ini memang milik vendor yang dimaksud
        if ($document->vendor_id !== $vendor->id) {
            return redirect()
                ->route('admin.vendors.show', $vendor)
                ->with('error', 'Dokumen tidak ditemukan untuk vendor ini.');
        }

        // Guard: pastikan file ada di storage
        if (!Storage::disk('local')->exists($document->file_path)) {
            return redirect()
                ->route('admin.vendors.show', $vendor)
                ->with('error', 'File tidak ditemukan di server. Mungkin sudah dihapus.');
        }

        // Stream file langsung ke browser — nama file pakai original name
        return Storage::disk('local')->download(
            $document->file_path,
            $document->file_name
        );
    }
}
