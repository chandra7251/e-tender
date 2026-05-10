<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TenderRequest;
use App\Http\Requests\Admin\TenderStatusRequest;
use App\Models\Tender;
use App\Models\TenderHistory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TenderController extends Controller
{
    private const VALID_STATUSES = ['draft', 'open', 'aanwijzing', 'bidding', 'closed', 'finished'];

    /**
     * List all tenders with optional filter and search.
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
     * Store a new tender.
     */
    public function store(TenderRequest $request): RedirectResponse
    {
        $tender = Tender::create([
            ...$request->validated(),
            'created_by' => auth()->id(),
        ]);

        TenderHistory::create([
            'tender_id'   => $tender->id,
            'actor_id'    => auth()->id(),
            'action'      => 'tender_created',
            'new_status'  => $tender->status,
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
     */
    public function edit(Tender $tender): View
    {
        return view('admin.tenders.edit', compact('tender'));
    }

    /**
     * Update an existing tender.
     */
    public function update(TenderRequest $request, Tender $tender): RedirectResponse
    {
        $tender->update($request->validated());

        return redirect()
            ->route('admin.tenders.show', $tender)
            ->with('success', 'Tender berhasil diperbarui.');
    }

    /**
     * Update tender status and record history.
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
