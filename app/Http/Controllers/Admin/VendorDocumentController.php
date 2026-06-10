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
     * Download dokumen vendor.
     */
    public function download(Vendor $vendor, VendorDocument $document): StreamedResponse|RedirectResponse
    {
        // Validasi kepemilikan dokumen
        if ($document->vendor_id !== $vendor->id) {
            return redirect()
                ->route('admin.vendors.show', $vendor)
                ->with('error', 'Dokumen tidak ditemukan untuk vendor ini.');
        }

        // Validasi eksistensi file
        if (!Storage::disk('local')->exists($document->file_path)) {
            return redirect()
                ->route('admin.vendors.show', $vendor)
                ->with('error', 'File tidak ditemukan di server. Mungkin sudah dihapus.');
        }

        // Stream file ke browser
        return Storage::disk('local')->download(
            $document->file_path,
            $document->file_name
        );
    }
}
