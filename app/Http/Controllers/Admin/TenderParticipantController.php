<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tender;
use Illuminate\View\View;

class TenderParticipantController extends Controller
{
    /**
     * List all vendors who joined a specific tender.
     */
    public function index(Tender $tender): View
    {
        $participants = $tender->participants()
            ->with(['vendor.user'])
            ->orderBy('joined_at')
            ->get();

        return view('admin.tenders.participants', compact('tender', 'participants'));
    }
}
