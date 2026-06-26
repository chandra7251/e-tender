<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bid;
use App\Models\PurchaseOrder;
use App\Models\Tender;
use App\Models\TenderParticipant;
use App\Models\TenderResult;
use App\Models\Vendor;
use App\Models\VendorSubmission;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // ── Basic Stats (existing) ──
        $stats = [
            'vendor_total'    => Vendor::count(),
            'vendor_pending'  => Vendor::where('verification_status', 'pending')->count(),
            'vendor_approved' => Vendor::where('verification_status', 'approved')->count(),
            'vendor_rejected' => Vendor::where('verification_status', 'rejected')->count(),
            'tender_total'    => Tender::count(),
            'tender_active'   => Tender::whereIn('status', ['open', 'aanwijzing', 'bidding'])->count(),
            'tender_finished' => Tender::where('status', 'finished')->count(),
            'bid_total'              => Bid::count(),
            'participant_total'      => TenderParticipant::count(),
            'active_bidding_tenders' => Tender::where('status', 'bidding')->count(),
            'lowest_bid'             => Bid::whereHas('tender', fn ($q) => $q->where('status', 'bidding'))
                                           ->min('bid_amount'),
        ];

        // ── Advanced Analytics ──

        // Total nilai kontrak (PO yang terbit)
        $stats['total_contract_value'] = PurchaseOrder::sum('amount');

        // Rata-rata bid per tender
        $stats['avg_bids_per_tender'] = Tender::has('bids')
            ->withCount('bids')
            ->get()
            ->avg('bids_count');

        // Rata-rata peserta per tender
        $stats['avg_participants_per_tender'] = Tender::has('participants')
            ->withCount('participants')
            ->get()
            ->avg('participants_count');

        // Penghematan (HPS/open_bidding_price vs winning bid)
        $savingsData = Tender::where('status', 'finished')
            ->whereNotNull('open_bidding_price')
            ->whereHas('result')
            ->with('result')
            ->get();

        $totalHPS = 0;
        $totalWinning = 0;
        foreach ($savingsData as $tender) {
            if ($tender->result && $tender->open_bidding_price > 0) {
                $totalHPS += $tender->open_bidding_price;
                $totalWinning += $tender->result->winning_bid_amount;
            }
        }
        $stats['total_hps'] = $totalHPS;
        $stats['total_winning'] = $totalWinning;
        $stats['total_savings'] = $totalHPS - $totalWinning;
        $stats['savings_percentage'] = $totalHPS > 0
            ? round((($totalHPS - $totalWinning) / $totalHPS) * 100, 1)
            : 0;

        // Pending submissions
        $stats['pending_submissions'] = VendorSubmission::where('status', 'pending')->count();

        // ── Tender Status Distribution (for pie chart) ──
        $tenderStatusDistribution = Tender::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        // ── Monthly Tender Trend (last 6 months) ──
        $monthlyTrend = Tender::select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                DB::raw('count(*) as total')
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // ── Top 5 Winning Vendors ──
        $topVendors = Vendor::withCount('wonResults')
            ->having('won_results_count', '>', 0)
            ->orderByDesc('won_results_count')
            ->limit(5)
            ->get();

        // ── Upcoming Tender Timeline (next 14 days) ──
        $upcomingTenders = Tender::whereIn('status', ['open', 'aanwijzing', 'bidding', 'draft'])
            ->where(function ($q) {
                $q->whereBetween('bidding_end', [now(), now()->addDays(14)])
                  ->orWhereBetween('bidding_start', [now(), now()->addDays(14)])
                  ->orWhereBetween('start_date', [now(), now()->addDays(14)]);
            })
            ->orderBy('bidding_end')
            ->limit(10)
            ->get(['id', 'title', 'status', 'start_date', 'bidding_start', 'bidding_end']);

        // ── Recent Activities ──
        $recentActivities = \App\Models\TenderHistory::with(['tender', 'actor'])
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.dashboard.index', compact(
            'stats',
            'tenderStatusDistribution',
            'monthlyTrend',
            'topVendors',
            'recentActivities',
            'upcomingTenders',
        ));
    }
}
