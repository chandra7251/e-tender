<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bid;
use App\Models\PurchaseOrder;
use App\Models\Tender;
use App\Models\TenderParticipant;
use App\Models\TenderResult;
use App\Models\Vendor;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DashboardApiController extends Controller
{
    public function stats(): JsonResponse
    {
        $tenderTotal    = Tender::count();
        $tenderActive   = Tender::whereIn('status', ['open', 'aanwijzing', 'bidding'])->count();
        $tenderFinished = Tender::where('status', 'finished')->count();

        $vendorTotal    = Vendor::count();
        $vendorApproved = Vendor::where('verification_status', 'approved')->count();
        $vendorPending  = Vendor::where('verification_status', 'pending')->count();

        $bidTotal         = Bid::count();
        $participantTotal = TenderParticipant::count();

        // Financial
        $totalHps           = Tender::where('status', 'finished')->sum('open_bidding_price') ?? 0;
        $totalContractValue = PurchaseOrder::sum('total_amount') ?? 0;
        $totalSavings       = $totalHps - $totalContractValue;
        $savingsPercentage  = $totalHps > 0 ? round(($totalSavings / $totalHps) * 100, 1) : 0;

        // Averages
        $avgBidsPerTender = $tenderTotal > 0 ? round($bidTotal / $tenderTotal, 1) : 0;

        // Status distribution
        $statusDist = Tender::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Top vendors
        $topVendors = Vendor::withCount('wonResults')
            ->having('won_results_count', '>', 0)
            ->orderByDesc('won_results_count')
            ->limit(5)
            ->get()
            ->map(fn ($v) => [
                'vendor_id'    => $v->id,
                'company_name' => $v->company_name,
                'wins'         => $v->won_results_count,
            ]);

        return response()->json([
            'tender_total'         => $tenderTotal,
            'tender_active'        => $tenderActive,
            'tender_finished'      => $tenderFinished,
            'vendor_total'         => $vendorTotal,
            'vendor_approved'      => $vendorApproved,
            'vendor_pending'       => $vendorPending,
            'bid_total'            => $bidTotal,
            'participant_total'    => $participantTotal,
            'total_hps'            => $totalHps,
            'total_contract_value' => $totalContractValue,
            'total_savings'        => $totalSavings,
            'savings_percentage'   => $savingsPercentage,
            'avg_bids_per_tender'  => $avgBidsPerTender,
            'status_distribution'  => $statusDist,
            'top_vendors'          => $topVendors,
        ]);
    }
}
