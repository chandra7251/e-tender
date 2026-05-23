<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bid;
use App\Models\Tender;
use App\Models\TenderParticipant;
use App\Models\Vendor;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            // Vendor counts
            'vendor_total'    => Vendor::count(),
            'vendor_pending'  => Vendor::where('verification_status', 'pending')->count(),
            'vendor_approved' => Vendor::where('verification_status', 'approved')->count(),
            'vendor_rejected' => Vendor::where('verification_status', 'rejected')->count(),

            // Tender counts
            'tender_total'    => Tender::count(),
            'tender_active'   => Tender::whereIn('status', ['open', 'aanwijzing', 'bidding'])->count(),
            'tender_finished' => Tender::where('status', 'finished')->count(),

            // Bid & Participant counts
            'bid_total'              => Bid::count(),
            'participant_total'      => TenderParticipant::count(),
            'active_bidding_tenders' => Tender::where('status', 'bidding')->count(),
            // FIX LOW-01: Ambil bid terendah hanya dari tender yang sedang aktif bidding
            'lowest_bid'             => Bid::whereHas('tender', fn ($q) => $q->where('status', 'bidding'))
                                           ->min('bid_amount'),
        ];

        return view('admin.dashboard.index', compact('stats'));
    }
}

