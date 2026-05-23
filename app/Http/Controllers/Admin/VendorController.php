<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\VendorVerificationRequest;
use App\Models\TenderHistory;
use App\Models\Vendor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VendorController extends Controller
{
    /**
     * List all vendors with optional filter and search.
     */
    public function index(Request $request): View
    {
        $query = Vendor::with('user')->latest();

        // Filter by verification status
        if ($request->filled('status') && in_array($request->status, ['pending', 'approved', 'rejected'])) {
            $query->where('verification_status', $request->status);
        }

        // Search by company name or user email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                  ->orWhereHas('user', fn ($u) => $u->where('email', 'like', "%{$search}%"));
            });
        }

        $vendors = $query->paginate(15)->withQueryString();

        return view('admin.vendors.index', compact('vendors'));
    }

    /**
     * Show vendor detail with documents.
     */
    public function show(Vendor $vendor): View
    {
        $vendor->load(['user', 'documents', 'verifier']);

        return view('admin.vendors.show', compact('vendor'));
    }

    /**
     * Approve a vendor.
     */
    public function approve(VendorVerificationRequest $request, Vendor $vendor): RedirectResponse
    {
        $vendor->update([
            'verification_status' => 'approved',
            'verification_notes'  => $request->notes,
            'verified_by'         => auth()->id(),
            'verified_at'         => now(),
        ]);

        // Log vendor_approved ke tender yang diikuti vendor (jika ada).
        // Catatan: tender_histories.tender_id is NOT NULL (schema constraint),
        // sehingga vendor-level events hanya bisa di-log jika vendor sudah join tender.
        $this->logVendorEvent($vendor, 'vendor_approved',
            "Vendor {$vendor->company_name} diapprove oleh admin."
        );

        return redirect()
            ->route('admin.vendors.show', $vendor)
            ->with('success', "Vendor {$vendor->company_name} berhasil diapprove.");
    }

    /**
     * Reject a vendor.
     */
    public function reject(VendorVerificationRequest $request, Vendor $vendor): RedirectResponse
    {
        $vendor->update([
            'verification_status' => 'rejected',
            'verification_notes'  => $request->notes,
            'verified_by'         => auth()->id(),
            'verified_at'         => now(),
        ]);

        // Log vendor_rejected ke tender yang diikuti vendor (jika ada).
        $this->logVendorEvent($vendor, 'vendor_rejected',
            "Vendor {$vendor->company_name} direject oleh admin." .
            ($request->notes ? " Alasan: {$request->notes}" : '')
        );

        return redirect()
            ->route('admin.vendors.show', $vendor)
            ->with('success', "Vendor {$vendor->company_name} berhasil direject.");
    }

    /**
     * Log a vendor verification event ke tender_histories.
     *
     * FIX HIGH-05 (setelah migration nullable): tender_id kini nullable sehingga
     * event vendor-level bisa dilog langsung ke tender_histories tanpa FK ke tender.
     * Jika vendor sudah join tender, log ke masing-masing tender (lebih kontekstual).
     */
    private function logVendorEvent(Vendor $vendor, string $action, string $description): void
    {
        $tenderIds = $vendor->tenderParticipants()->pluck('tender_id');

        if ($tenderIds->isEmpty()) {
            // Vendor belum join tender manapun — log dengan tender_id = null.
            // Migration 2026_05_13_050000 sudah membuat kolom ini nullable.
            TenderHistory::create([
                'tender_id'   => null,
                'actor_id'    => auth()->id(),
                'action'      => $action,
                'description' => $description,
                'metadata'    => ['vendor_id' => $vendor->id, 'company_name' => $vendor->company_name],
                'created_at'  => now(),
            ]);
            return;
        }

        // Vendor sudah join tender — log ke setiap tender yang diikuti.
        foreach ($tenderIds as $tenderId) {
            TenderHistory::create([
                'tender_id'   => $tenderId,
                'actor_id'    => auth()->id(),
                'action'      => $action,
                'description' => $description,
                'metadata'    => ['vendor_id' => $vendor->id, 'company_name' => $vendor->company_name],
                'created_at'  => now(),
            ]);
        }
    }
}
