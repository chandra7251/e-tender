<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Vendor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VendorBlacklistController extends Controller
{
    /**
     * Show blacklist management page.
     */
    public function index(Request $request): View
    {
        $query = Vendor::with(['user', 'blacklister']);

        if ($request->input('filter') === 'blacklisted') {
            $query->where('is_blacklisted', true);
        } elseif ($request->input('filter') === 'active') {
            $query->where('is_blacklisted', false)->where('verification_status', 'approved');
        }

        if ($request->filled('search')) {
            $query->where('company_name', 'like', "%{$request->search}%");
        }

        $vendors = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total_blacklisted' => Vendor::where('is_blacklisted', true)->count(),
            'total_active'      => Vendor::where('is_blacklisted', false)->where('verification_status', 'approved')->count(),
        ];

        return view('admin.vendors.blacklist', compact('vendors', 'stats'));
    }

    /**
     * Blacklist a vendor.
     */
    public function blacklist(Request $request, Vendor $vendor): RedirectResponse
    {
        $validated = $request->validate([
            'blacklist_reason' => 'required|string|max:1000',
        ]);

        if ($vendor->is_blacklisted) {
            return redirect()->back()->with('error', 'Vendor sudah dalam daftar blacklist.');
        }

        $vendor->update([
            'is_blacklisted'  => true,
            'blacklist_reason' => $validated['blacklist_reason'],
            'blacklisted_at'   => now(),
            'blacklisted_by'   => auth()->id(),
        ]);

        ActivityLog::log(
            action: 'vendor_blacklisted',
            module: 'vendor',
            description: "Vendor \"{$vendor->company_name}\" di-blacklist. Alasan: {$validated['blacklist_reason']}",
            subjectType: Vendor::class,
            subjectId: $vendor->id,
        );

        return redirect()->back()
            ->with('success', "Vendor \"{$vendor->company_name}\" berhasil di-blacklist.");
    }

    /**
     * Remove vendor from blacklist.
     */
    public function unblacklist(Request $request, Vendor $vendor): RedirectResponse
    {
        if (!$vendor->is_blacklisted) {
            return redirect()->back()->with('error', 'Vendor tidak dalam daftar blacklist.');
        }

        $oldReason = $vendor->blacklist_reason;

        $vendor->update([
            'is_blacklisted'   => false,
            'blacklist_reason' => null,
            'blacklisted_at'   => null,
            'blacklisted_by'   => null,
        ]);

        ActivityLog::log(
            action: 'vendor_unblacklisted',
            module: 'vendor',
            description: "Vendor \"{$vendor->company_name}\" dihapus dari blacklist. Alasan sebelumnya: {$oldReason}",
            subjectType: Vendor::class,
            subjectId: $vendor->id,
        );

        return redirect()->back()
            ->with('success', "Vendor \"{$vendor->company_name}\" berhasil dihapus dari blacklist.");
    }
}
