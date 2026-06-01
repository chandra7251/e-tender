<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VendorSubmission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubmissionController extends Controller
{
    /**
     * List semua pengajuan vendor dari mobile app.
     * Bisa filter by status: ?status=pending|approved|rejected
     */
    public function index(Request $request): View
    {
        $query = VendorSubmission::with(['vendor.user', 'reviewer'])
            ->orderByDesc('created_at');

        if ($request->filled('status') && in_array($request->status, ['pending', 'approved', 'rejected'])) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%")
                  ->orWhere('kategori', 'like', "%{$search}%")
                  ->orWhereHas('vendor', fn ($v) => $v->where('company_name', 'like', "%{$search}%"));
            });
        }

        $submissions = $query->paginate(15)->withQueryString();

        $counts = [
            'pending'  => VendorSubmission::where('status', 'pending')->count(),
            'approved' => VendorSubmission::where('status', 'approved')->count(),
            'rejected' => VendorSubmission::where('status', 'rejected')->count(),
            'total'    => VendorSubmission::count(),
        ];

        return view('admin.submissions.index', compact('submissions', 'counts'));
    }

    /**
     * Detail satu pengajuan beserta foto-fotonya.
     */
    public function show(VendorSubmission $submission): View
    {
        $submission->load(['vendor.user', 'photos', 'reviewer']);

        return view('admin.submissions.show', compact('submission'));
    }

    /**
     * Admin menyetujui pengajuan vendor.
     */
    public function approve(VendorSubmission $submission): RedirectResponse
    {
        if ($submission->status !== 'pending') {
            return back()->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
        }

        $submission->update([
            'status'      => 'approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return redirect()
            ->route('admin.submissions.show', $submission)
            ->with('success', "Pengajuan \"{$submission->nama_barang}\" berhasil disetujui.");
    }

    /**
     * Admin menolak pengajuan vendor. Wajib mengisi catatan alasan penolakan.
     */
    public function reject(Request $request, VendorSubmission $submission): RedirectResponse
    {
        $request->validate([
            'catatan_admin' => 'required|string|min:10|max:500',
        ], [
            'catatan_admin.required' => 'Alasan penolakan wajib diisi.',
            'catatan_admin.min'      => 'Alasan penolakan minimal 10 karakter.',
        ]);

        if ($submission->status !== 'pending') {
            return back()->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
        }

        $submission->update([
            'status'        => 'rejected',
            'catatan_admin' => $request->catatan_admin,
            'reviewed_by'   => auth()->id(),
            'reviewed_at'   => now(),
        ]);

        return redirect()
            ->route('admin.submissions.show', $submission)
            ->with('success', "Pengajuan \"{$submission->nama_barang}\" berhasil ditolak.");
    }
}
