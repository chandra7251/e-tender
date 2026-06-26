<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\TenderComplaint;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ComplaintController extends Controller
{
    public function index(Request $request): View
    {
        $q = TenderComplaint::with(['tender:id,title', 'vendor.user:id,name'])->latest();

        if ($request->filled('status')) {
            $q->where('status', $request->status);
        }

        $complaints = $q->paginate(20)->withQueryString();
        return view('admin.complaints.index', compact('complaints'));
    }

    public function respond(Request $request, int $id): RedirectResponse
    {
        $c = TenderComplaint::findOrFail($id);

        $request->validate([
            'response' => 'required|string|max:2000',
            'status'   => 'required|in:reviewed,accepted,rejected',
        ]);

        $c->update([
            'response'     => $request->response,
            'status'       => $request->status,
            'responded_by' => auth()->id(),
            'responded_at' => now(),
        ]);

        return redirect()->route('admin.complaints.index')
            ->with('success', 'Sanggahan berhasil ditanggapi.');
    }
}
