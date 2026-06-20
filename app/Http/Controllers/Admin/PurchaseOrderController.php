<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PurchaseOrderRequest;
use App\Models\PurchaseOrder;
use App\Models\Tender;
use App\Models\TenderHistory;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PurchaseOrderController extends Controller
{
    /**
     * Show the create PO form.
     */
    public function create(Tender $tender): View
    {
        // Validasi status tender
        if ($tender->status !== 'finished') {
            return redirect()->back()->with('error', "PO hanya bisa dibuat setelah tender berstatus 'finished'. Status saat ini: '{$tender->status}'.");
        }

        $result = $tender->result()->with(['winner'])->first();

        if (is_null($result)) {
            return redirect()->back()->with('error', 'Pilih pemenang tender terlebih dahulu sebelum membuat PO.');
        }
        
        if ($tender->purchaseOrder()->exists()) {
            return redirect()->back()->with('error', 'PO untuk tender ini sudah dibuat.');
        }

        // Suggest a PO number
        $suggestedPoNumber = 'PO-' . strtoupper(substr(md5($tender->id . now()), 0, 8));

        return view('admin.purchase-orders.create', compact('tender', 'result', 'suggestedPoNumber'));
    }

    /**
     * Store a new Purchase Order.
     */
    public function store(PurchaseOrderRequest $request, Tender $tender): RedirectResponse
    {
        // Validasi status tender
        if ($tender->status !== 'finished') {
            return redirect()->back()->with('error', "PO hanya bisa dibuat setelah tender berstatus 'finished'.");
        }

        $result = $tender->result()->with(['winner'])->first();

        if (is_null($result)) {
            return redirect()->back()->with('error', 'Pilih pemenang tender terlebih dahulu.');
        }
        
        if ($tender->purchaseOrder()->exists()) {
            return redirect()->back()->with('error', 'PO untuk tender ini sudah ada.');
        }

        PurchaseOrder::create([
            'tender_result_id' => $result->id,
            'tender_id'        => $tender->id,
            'vendor_id'        => $result->winner_vendor_id,
            'po_number'        => $request->input('po_number'),
            'amount'           => $request->input('amount'),
            'issued_date'      => $request->input('issued_date'),
            'notes'            => $request->input('notes'),
            'generated_by'     => auth()->id(),
        ]);

        TenderHistory::create([
            'tender_id'   => $tender->id,
            'actor_id'    => auth()->id(),
            'action'      => 'po_generated',
            'old_status'  => $tender->status,
            'new_status'  => $tender->status,
            'description' => "PO {$request->input('po_number')} diterbitkan.",
            'created_at'  => now(),
        ]);

        return redirect()
            ->route('admin.tenders.purchase-order.show', $tender)
            ->with('success', 'Purchase Order berhasil dibuat.');
    }

    /**
     * Show the Purchase Order.
     */
    public function show(Tender $tender): View
    {
        $po = $tender->purchaseOrder()->with(['vendor.user', 'tender', 'generator'])->first();

        abort_if(is_null($po), 404, 'PO belum dibuat untuk tender ini.');

        return view('admin.purchase-orders.show', compact('tender', 'po'));
    }

    /**
     * Download the Purchase Order as PDF.
     */
    public function downloadPdf(Tender $tender)
    {
        $po = $tender->purchaseOrder()->with(['vendor.user', 'tender', 'generator'])->first();

        if (is_null($po)) {
            return redirect()->back()->with('error', 'PO belum dibuat untuk tender ini.');
        }

        try {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.purchase-orders.pdf', compact('tender', 'po'));

            // Output configuration
            $pdf->setPaper('A4', 'portrait');

            return $pdf->download("PO-{$po->po_number}.pdf");
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Gagal generate PDF PO', [
                'tender_id' => $tender->id,
                'po_id'     => $po->id,
                'error'     => $e->getMessage(),
            ]);

            return redirect()->back()->with('error', 'Gagal mengunduh PDF. Pastikan ekstensi server mendukung atau coba beberapa saat lagi.');
        }
    }
}
