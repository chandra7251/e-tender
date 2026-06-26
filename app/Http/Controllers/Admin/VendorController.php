<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\VendorVerificationRequest;
use App\Models\ActivityLog;
use App\Models\TenderHistory;
use App\Models\Vendor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
class VendorController extends Controller
{
    public function index(Request $request): View
    {
        $query = Vendor::with('user')->latest();
        if ($request->filled('status') && in_array($request->status, ['pending', 'approved', 'rejected'])) {
            $query->where('verification_status', $request->status);
        }
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
    public function show(Vendor $vendor): View
    {
        $vendor->load(['user', 'documents', 'verifier']);
        return view('admin.vendors.show', compact('vendor'));
    }
    public function approve(VendorVerificationRequest $request, Vendor $vendor): RedirectResponse
    {
        $vendor->update([
            'verification_status' => 'approved',
            'verification_notes'  => $request->notes,
            'verified_by'         => auth()->id(),
            'verified_at'         => now(),
        ]);
        $this->logVendorEvent($vendor, 'vendor_approved',
            "Vendor {$vendor->company_name} diapprove oleh admin."
        );
        ActivityLog::log('vendor_approved', 'vendor',
            "Vendor \"{$vendor->company_name}\" diapprove.",
            subjectType: Vendor::class, subjectId: $vendor->id,
        );
        return redirect()
            ->route('admin.vendors.show', $vendor)
            ->with('success', "Vendor {$vendor->company_name} berhasil diapprove.");
    }
    public function reject(VendorVerificationRequest $request, Vendor $vendor): RedirectResponse
    {
        $vendor->update([
            'verification_status' => 'rejected',
            'verification_notes'  => $request->notes,
            'verified_by'         => auth()->id(),
            'verified_at'         => now(),
        ]);
        $this->logVendorEvent($vendor, 'vendor_rejected',
            "Vendor {$vendor->company_name} direject oleh admin." .
            ($request->notes ? " Alasan: {$request->notes}" : '')
        );
        ActivityLog::log('vendor_rejected', 'vendor',
            "Vendor \"{$vendor->company_name}\" direject." . ($request->notes ? " Alasan: {$request->notes}" : ''),
            subjectType: Vendor::class, subjectId: $vendor->id,
        );
        return redirect()
            ->route('admin.vendors.show', $vendor)
            ->with('success', "Vendor {$vendor->company_name} berhasil direject.");
    }
    private function logVendorEvent(Vendor $vendor, string $action, string $description): void
    {
        $tenderIds = $vendor->tenderParticipants()->pluck('tender_id');
        if ($tenderIds->isEmpty()) {
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
