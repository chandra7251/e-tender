<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TenderAnnouncementRequest;
use App\Models\Tender;
use App\Models\TenderAnnouncement;
use Illuminate\Http\RedirectResponse;

class TenderAnnouncementController extends Controller
{
    /**
     * Store a new announcement/aanwijzing for a tender.
     */
    public function store(TenderAnnouncementRequest $request, Tender $tender): RedirectResponse
    {
        TenderAnnouncement::create([
            'tender_id'    => $tender->id,
            'created_by'   => auth()->id(),
            'title'        => $request->input('title'),
            'content'      => $request->input('content'),
            'published_at' => $request->input('published_at'),
        ]);

        return redirect()
            ->route('admin.tenders.show', $tender)
            ->with('success', 'Aanwijzing / Pengumuman berhasil ditambahkan.');
    }
}
