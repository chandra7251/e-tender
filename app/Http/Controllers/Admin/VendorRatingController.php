<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Tender;
use App\Models\Vendor;
use App\Models\VendorRating;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VendorRatingController extends Controller
{
    /**
     * Show rating form for a vendor after a tender completes.
     */
    public function create(Tender $tender, Vendor $vendor): View|RedirectResponse
    {
        if ($tender->status !== 'finished') {
            return redirect()->route('admin.tenders.show', $tender)
                ->with('error', 'Rating hanya bisa diberikan setelah tender selesai (finished).');
        }

        // Check vendor participated in this tender
        $participated = $tender->participants()->where('vendor_id', $vendor->id)->exists();
        if (!$participated) {
            return redirect()->back()->with('error', 'Vendor ini tidak berpartisipasi di tender ini.');
        }

        $existingRating = VendorRating::where('vendor_id', $vendor->id)
            ->where('tender_id', $tender->id)
            ->first();

        return view('admin.vendors.rating', compact('tender', 'vendor', 'existingRating'));
    }

    /**
     * Store or update vendor rating.
     */
    public function store(Request $request, Tender $tender, Vendor $vendor): RedirectResponse
    {
        $validated = $request->validate([
            'quality_score'       => 'required|integer|min:1|max:5',
            'delivery_score'      => 'required|integer|min:1|max:5',
            'communication_score' => 'required|integer|min:1|max:5',
            'compliance_score'    => 'required|integer|min:1|max:5',
            'review'              => 'nullable|string|max:1000',
        ]);

        $rating = VendorRating::updateOrCreate(
            ['vendor_id' => $vendor->id, 'tender_id' => $tender->id],
            [
                ...$validated,
                'rated_by' => auth()->id(),
            ]
        );

        ActivityLog::log(
            action: 'vendor_rated',
            module: 'vendor',
            description: "Rating vendor \"{$vendor->company_name}\" untuk tender \"{$tender->title}\": "
                . "skor {$rating->overall_score}/5.",
            subjectType: Vendor::class,
            subjectId: $vendor->id,
        );

        return redirect()
            ->route('admin.tenders.show', $tender)
            ->with('success', "Rating untuk {$vendor->company_name} berhasil disimpan (Skor: {$rating->overall_score}/5).");
    }

    /**
     * Show all ratings for a specific vendor (vendor profile page).
     */
    public function vendorRatings(Vendor $vendor): View
    {
        $ratings = $vendor->ratings()
            ->with(['tender', 'rater'])
            ->latest()
            ->paginate(10);

        $avgRating = $vendor->average_rating;

        return view('admin.vendors.ratings-list', compact('vendor', 'ratings', 'avgRating'));
    }
}
