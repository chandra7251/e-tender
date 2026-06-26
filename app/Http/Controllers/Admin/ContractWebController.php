<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Contract;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ContractWebController extends Controller
{
    public function index(Request $request): View
    {
        $q = Contract::with(['tender:id,title', 'vendor.user:id,name'])->latest();

        if ($request->filled('status')) {
            $q->where('status', $request->status);
        }

        $contracts = $q->paginate(20)->withQueryString();
        return view('admin.contracts.index', compact('contracts'));
    }

    public function show(int $id): View
    {
        $contract = Contract::with(['tender:id,title', 'vendor.user:id,name', 'deliveries'])
            ->findOrFail($id);
        return view('admin.contracts.show', compact('contract'));
    }

    public function send(int $id): RedirectResponse
    {
        $contract = Contract::findOrFail($id);
        if ($contract->status !== 'draft') {
            return redirect()->back()->with('error', 'Kontrak hanya bisa dikirim jika berstatus Draft.');
        }
        $contract->update(['status' => 'sent_to_vendor']);
        return redirect()->back()->with('success', 'Kontrak berhasil dikirim ke vendor.');
    }

    public function sign(int $id): RedirectResponse
    {
        $contract = Contract::findOrFail($id);
        if ($contract->status !== 'signed_vendor') {
            return redirect()->back()->with('error', 'Vendor belum menandatangani kontrak.');
        }
        $contract->update([
            'status'          => 'active',
            'admin_signed_at' => now(),
        ]);
        return redirect()->back()->with('success', 'Kontrak ditandatangani dan diaktifkan.');
    }

    public function complete(int $id): RedirectResponse
    {
        $contract = Contract::findOrFail($id);
        if ($contract->status !== 'active') {
            return redirect()->back()->with('error', 'Kontrak hanya bisa diselesaikan jika berstatus Aktif.');
        }
        $contract->update(['status' => 'completed']);
        return redirect()->back()->with('success', 'Kontrak berhasil diselesaikan.');
    }
}
