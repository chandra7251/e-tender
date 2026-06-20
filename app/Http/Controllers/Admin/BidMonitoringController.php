<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bid;
use App\Models\Tender;
use Illuminate\View\View;

class BidMonitoringController extends Controller
{
    public function index(Tender $tender): View
    {
        $bids = $tender->bids()
            ->with(['vendor.user'])
            ->orderBy('bid_amount', 'asc')
            ->get();

        $lowestBidId = $bids->first()?->id;

        return view('admin.bids.index', compact('tender', 'bids', 'lowestBidId'));
    }

    public function histories(Tender $tender, Bid $bid): View
    {
        abort_if($bid->tender_id !== $tender->id, 404);

        $histories = $bid->histories()
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.bids.histories', compact('tender', 'bid', 'histories'));
    }
}
