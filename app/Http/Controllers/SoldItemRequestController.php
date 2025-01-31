<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ItemRequest; // Make sure you have this import
use App\Models\GoldItemSold;
use App\Models\GoldItem; // Import GoldItem
use App\Models\SoldItemRequest;
use App\Models\SaleRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Services\SellService;

// ... other imports

class SoldItemRequestController extends Controller
{
    private SellService $saleService;
    public function __construct(
        SellService $saleService,
    ) {
        $this->saleService = $saleService;
    }

    public function viewSaleRequests()
    {
        $soldItemRequests = SaleRequest::where('status', 'pending')->get();
        return view('admin.Requests.sold_requests', compact('soldItemRequests'));
    }
    public function viewAllSaleRequests()
    {
        $soldItemRequests = SaleRequest::all();
        return view('shops.all_sale_requests', compact('soldItemRequests'));
    }
    public function approveSaleRequest($id)
    {
        $request = SaleRequest::findOrFail($id);
        $request->update([
            'status' => 'approved',
            'approver_shop_name' => Auth::user()->shop_name
        ]);
        $this->saleService->approveSale($request);
        return redirect()->back()->with('success', 'Sale request approved and item marked as sold');
    }


    public function rejectSaleRequest($id)
    {
        $request = SaleRequest::findOrFail($id);
        $request->update([
            'status' => 'rejected',
            'approver_shop_name' => Auth::user()->shop_name
        ]);
        return redirect()->back()->with('success', 'Sale request rejected');
    }
}
