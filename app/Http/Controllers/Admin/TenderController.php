<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TenderRequest;
use App\Http\Requests\Admin\TenderStatusRequest;
use App\Models\Tender;
use App\Models\TenderHistory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class TenderController extends Controller
{
    // Status yang masih boleh diedit datanya (sebelum bidding aktif)
    private const EDITABLE_STATUSES = ['draft', 'open', 'aanwijzing'];

    private const VALID_STATUSES = ['draft', 'open', 'aanwijzing', 'bidding', 'closed', 'finished'];

    /**
     * List all tenders dengan optional filter dan search.
     */
    public function index(Request $request): View
    {
        $query = Tender::with('creator')->latest();

        if ($request->filled('status') && in_array($request->status, self::VALID_STATUSES)) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('title', 'like', "%{$request->search}%");
        }

        $tenders = $query->paginate(15)->withQueryString();

        return view('admin.tenders.index', compact('tenders'));
    }

    /**
     * Show create tender form.
     */
    public function create(): View
    {
        return view('admin.tenders.create');
    }

    /**
     * Store a new tender — selalu dibuat dengan status 'draft'.
     */
    public function store(TenderRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('tenders/photos', 'public');
        }
        unset($data['photo']); // tidak ada kolom 'photo', yg disimpan 'photo_path'

        // FIX MED-04: Paksa status draft saat create
        $tender = Tender::create([
            ...$data,
            'created_by' => auth()->id(),
            'status'     => 'draft',
        ]);

        TenderHistory::create([
            'tender_id'   => $tender->id,
            'actor_id'    => auth()->id(),
            'action'      => 'tender_created',
            'new_status'  => 'draft',
            'description' => 'Tender dibuat oleh admin.',
            'created_at'  => now(),
        ]);

        return redirect()
            ->route('admin.tenders.show', $tender)
            ->with('success', 'Tender berhasil dibuat.');
    }

    /**
     * Show tender detail.
     */
    public function show(Tender $tender): View
    {
        $tender->load(['creator', 'announcements.creator', 'participants.vendor', 'histories.actor']);

        return view('admin.tenders.show', compact('tender'));
    }

    /**
     * Show edit tender form.
     * FIX BUG-02: Larang akses form edit jika tender sudah live.
     */
    public function edit(Tender $tender): View|RedirectResponse
    {
        if (!in_array($tender->status, self::EDITABLE_STATUSES)) {
            return redirect()
                ->route('admin.tenders.show', $tender)
                ->with('error', "Tender tidak bisa diedit saat berstatus '{$tender->status}'.");
        }

        return view('admin.tenders.edit', compact('tender'));
    }

    /**
     * Update an existing tender.
     * FIX BUG-02: Guard — tidak bisa edit data saat tender sedang aktif (bidding/closed/finished).
     */
    public function update(TenderRequest $request, Tender $tender): RedirectResponse
    {
        // Guard: data tender tidak boleh diubah saat sudah live
        if (!in_array($tender->status, self::EDITABLE_STATUSES)) {
            return redirect()
                ->route('admin.tenders.show', $tender)
                ->with('error', "Data tender tidak dapat diubah saat status '{$tender->status}'. Gunakan ubah status.");
        }

        $data = $request->validated();

        // Handle photo upload — hapus foto lama jika ada foto baru
        if ($request->hasFile('photo')) {
            if ($tender->photo_path) {
                Storage::disk('public')->delete($tender->photo_path);
            }
            $data['photo_path'] = $request->file('photo')->store('tenders/photos', 'public');
        }
        unset($data['photo']);

        $oldStatus = $tender->status;
        $tender->update($data);

        TenderHistory::create([
            'tender_id'   => $tender->id,
            'actor_id'    => auth()->id(),
            'action'      => 'tender_updated',
            'old_status'  => $oldStatus,
            'new_status'  => $tender->fresh()->status,
            'description' => "Data tender \"{$tender->title}\" diperbarui oleh admin.",
            'created_at'  => now(),
        ]);

        return redirect()
            ->route('admin.tenders.show', $tender)
            ->with('success', 'Tender berhasil diperbarui.');
    }

    /**
     * Update tender status menggunakan state machine.
     * FIX HIGH-01: Transisi status divalidasi oleh TenderStatusRequest.
     */
    public function updateStatus(TenderStatusRequest $request, Tender $tender): RedirectResponse
    {
        $oldStatus = $tender->status;
        $newStatus = $request->status;

        $tender->update(['status' => $newStatus]);

        TenderHistory::create([
            'tender_id'   => $tender->id,
            'actor_id'    => auth()->id(),
            'action'      => 'status_changed',
            'old_status'  => $oldStatus,
            'new_status'  => $newStatus,
            'description' => $request->description
                ?? "Status tender diubah dari {$oldStatus} menjadi {$newStatus}.",
            'created_at'  => now(),
        ]);

        return redirect()
            ->route('admin.tenders.show', $tender)
            ->with('success', "Status tender berhasil diubah menjadi {$newStatus}.");
    }
}
