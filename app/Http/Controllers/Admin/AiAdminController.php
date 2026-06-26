<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Tender;
use Illuminate\Http\Request;

class AiAdminController extends Controller
{
    public function index()
    {
        $tenders = Tender::whereIn('status', ['bidding','evaluation','finished'])
            ->select('id','title','open_bidding_price','status','evaluation_method')->latest()->get();
        return view('admin.ai.index', compact('tenders'));
    }
}
