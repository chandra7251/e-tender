<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TenderRequest;
use App\Http\Requests\Admin\TenderStatusRequest;
use App\Models\Tender;
use App\Models\TenderHistory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class TenderController extends Controller
{
    // Status yang bisa diedit
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

        // tidak ada kolom 'photo' lagi di validasi, dihapus

        // Status awal draft
        $tender = Tender::create([
            ...$data,
            'created_by' => auth()->id(),
            'status'     => 'draft',
        ]);

        // Handle multi-photo upload
        // CATATAN: Jika upload gagal diam-diam (PHP post_max_size terlampaui),
        // $request->hasFile() akan false dan section ini di-skip.
        // Cek php.ini: upload_max_filesize dan post_max_size harus ≥ jumlah total foto.
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                // store() mengembalikan false jika gagal simpan ke disk
                $path = $photo->store('tenders/photos', 'public');

                if ($path === false) {
                    // Gagal simpan ke storage — log error dan lanjutkan ke foto berikutnya
                    Log::error('Gagal simpan foto tender ke storage', [
                        'tender_id'     => $tender->id,
                        'original_name' => $photo->getClientOriginalName(),
                        'size'          => $photo->getSize(),
                    ]);
                    continue;
                }

                $tender->photos()->create(['photo_path' => $path]);
            }
        } else {
            // Log untuk bantu diagnosa: cek apakah photo field ada di request
            Log::debug('Admin store tender: tidak ada file foto di request', [
                'tender_id'    => $tender->id,
                'has_photos'   => $request->has('photos'),
                'content_type' => $request->header('Content-Type'),
            ]);
        }

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
        $tender->load(['creator', 'announcements.creator', 'participants.vendor', 'histories.actor', 'photos']);

        return view('admin.tenders.show', compact('tender'));
    }

    /**
     * Show edit tender form.
     */
    public function edit(Tender $tender): View|RedirectResponse
    {
        if (!in_array($tender->status, self::EDITABLE_STATUSES)) {
            return redirect()
                ->route('admin.tenders.show', $tender)
                ->with('error', "Tender tidak bisa diedit saat berstatus '{$tender->status}'.");
        }

        $tender->load('photos');
        return view('admin.tenders.edit', compact('tender'));
    }

    /**
     * Update an existing tender.
     */
    public function update(TenderRequest $request, Tender $tender): RedirectResponse
    {
        // Validasi status untuk update
        if (!in_array($tender->status, self::EDITABLE_STATUSES)) {
            return redirect()
                ->route('admin.tenders.show', $tender)
                ->with('error', "Data tender tidak dapat diubah saat status '{$tender->status}'. Gunakan ubah status.");
        }

        $data = $request->validated();

        // Validasi jumlah foto agar tidak melebihi 3
        if ($request->hasFile('photos')) {
            $existingCount = $tender->photos()->count();
            $newCount = count($request->file('photos'));
            if (($existingCount + $newCount) > 3) {
                return back()->withInput()->withErrors([
                    'photos' => "Total maksimal foto adalah 3. Saat ini Anda memiliki {$existingCount} foto."
                ]);
            }

            foreach ($request->file('photos') as $photo) {
                // store() mengembalikan false jika gagal simpan ke disk
                $path = $photo->store('tenders/photos', 'public');

                if ($path === false) {
                    Log::error('Gagal simpan foto tender ke storage (update)', [
                        'tender_id'     => $tender->id,
                        'original_name' => $photo->getClientOriginalName(),
                    ]);
                    continue;
                }

                $tender->photos()->create(['photo_path' => $path]);
            }
        }

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
     * Update tender status.
     */
    public function updateStatus(TenderStatusRequest $request, Tender $tender): RedirectResponse
    {
        $oldStatus = $tender->status;
        $newStatus = $request->status;
        $description = $request->description ?? "Status tender diubah dari {$oldStatus} menjadi {$newStatus}.";

        $tender->update(['status' => $newStatus]);

        TenderHistory::create([
            'tender_id'   => $tender->id,
            'actor_id'    => auth()->id(),
            'action'      => 'status_changed',
            'old_status'  => $oldStatus,
            'new_status'  => $newStatus,
            'description' => $description,
            'created_at'  => now(),
        ]);

        // Send Notification
        $usersToNotify = collect();

        if ($oldStatus === 'draft' && $newStatus === 'open') {
            // Send to all vendors
            $usersToNotify = \App\Models\User::where('role', 'vendor')->get();
        } else {
            // Send only to participating vendors
            $usersToNotify = $tender->participants()->with('vendor.user')->get()->pluck('vendor.user')->filter();
        }

        if ($usersToNotify->isNotEmpty()) {
            \Illuminate\Support\Facades\Notification::send(
                $usersToNotify,
                new \App\Notifications\TenderStatusChanged($tender, $oldStatus, $newStatus, $description)
            );
        }

        return redirect()
            ->route('admin.tenders.show', $tender)
            ->with('success', "Status tender berhasil diubah menjadi {$newStatus}.");
    }

    /**
     * Delete an individual tender photo.
     */
    public function deletePhoto(Tender $tender, \App\Models\TenderPhoto $photo): RedirectResponse
    {
        if ($photo->tender_id !== $tender->id) {
            abort(404);
        }

        if ($photo->photo_path) {
            Storage::disk('public')->delete($photo->photo_path);
        }
        $photo->delete();

        return back()->with('success', 'Foto berhasil dihapus.');
    }
}
