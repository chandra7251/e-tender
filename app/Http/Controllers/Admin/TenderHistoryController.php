<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tender;
use Illuminate\View\View;

class TenderHistoryController extends Controller
{
    /**
     * Show the full activity history for a tender.
     */
    public function index(Tender $tender): View
    {
        $histories = $tender->histories()
            ->with(['actor'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.tender-histories.index', compact('tender', 'histories'));
    }
}
