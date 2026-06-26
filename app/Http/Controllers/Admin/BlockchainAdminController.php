<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\BlockchainRecord;

class BlockchainAdminController extends Controller
{
    public function index()
    {
        $records = BlockchainRecord::latest()->paginate(20);
        return view('admin.blockchain.index', compact('records'));
    }
}
