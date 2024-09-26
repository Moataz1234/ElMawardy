<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\GoldItem;
use App\Models\Shop;
use App\Models\TransferRequest;
use App\Notifications\TransferRequestNotification;
use Illuminate\Support\Facades\Notification;

class ShopsController extends Controller
{
    public function transferRequest(Request $request, $id)
    {
    $goldItem = GoldItem::findOrFail($id);

    // Create a new transfer request
   $transferRequest= TransferRequest::create([
        'gold_item_id' => $goldItem->id,
        'from_shop_id' => $goldItem->shop_id,
        'to_shop_id' => $request->input('shop_id'),
        'status' => 'pending'
    ]);

    return redirect()->back()->with('message', 'Transfer request sent to the shop.');
}
public function handleTransferRequest($id, $status)
{
    $transferRequest = TransferRequest::findOrFail($id);

    if ($status === 'accepted') {
        // Update the gold item's shop ID to the new shop
        $goldItem = $transferRequest->goldItem;
        $goldItem->shop_id = $transferRequest->to_shop_id;
        $goldItem->save();
    }

    // Update the request status
    $transferRequest->status = $status;
    $transferRequest->save();

    return redirect()->route('transfer.requests')->with('message', 'Transfer request has been ' . $status);
}
public function viewTransferRequests()
{
    $user = Auth::user();
    $shop = $user->shop; // Ensure the user has a relation to a shop

    // Fetch all pending transfer requests directed to the user's shop
    $transferRequests = TransferRequest::with(['goldItem', 'fromShop', 'toShop'])
                            ->where('to_shop_id', $shop->id)
                            ->where('status', 'pending')
                            ->get();

    // Pass the data to the view
    return view('shops.transfer_requests.index', compact('transferRequests'));
}
public function showTransferForm(string $id)
{
    $goldItem = GoldItem::findOrFail($id);
    $shops = Shop::all(); // Assuming you have a Shop model
    return view('admin.Gold.Gold_transfer', compact('goldItem', 'shops'));
}
    // public function shopView(Request $request, $shopId)
    // {
    //     // Ensure the logged-in user is from the correct shop
    //     if (Auth::user()->shop_id != $shopId) {
    //         abort(403, 'Unauthorized access.');
    //     }
    
    //     // Fetch only the gold items related to the shop by shop_id
    //     $goldItems = GoldItem::where('shop_id', $shopId);
    
    //     // Apply sorting if requested
    //     if ($request->has('sort') && $request->has('direction')) {
    //         $goldItems->orderBy($request->sort, $request->direction);
    //     }
    
    //     // Apply search functionality if needed
    //     if ($request->has('search')) {
    //         $goldItems->where(function ($q) use ($request) {
    //             $q->where('serial_number', 'LIKE', "%{$request->search}%")
    //               ->orWhere('kind', 'LIKE', "%{$request->search}%")
    //               ->orWhere('model', 'LIKE', "%{$request->search}%");
    //         });
    //     }
    
    //     // Paginate the results
    //     $goldItems = $goldItems->paginate(10);
    
    //     return view('shops.index', compact('goldItems', 'shopId'));
    // }
  
     public function showShopItems(Request $request)
    {
        $user = Auth::user(); // Get the authenticated user
    
        $search = $request->input('search');
        $sort = $request->input('sort', 'serial_number');
        $direction = $request->input('direction', 'asc');

    
        // Fetch only the items requested by the logged-in user
        $goldItems = GoldItem::whereHas('transferRequests', function ($query) use ($user) {
            $query->where('to_shop_id', $user->shop_id);
        })
        ->when($search, function ($query, $search) {
            return $query->where('serial_number', 'like', "%{$search}%")
                ->orWhereHas('shop', function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%");
                })
                ->orWhere('kind', 'like', "%{$search}%")
                ->orWhere('model', 'like', "%{$search}%")
                ->orWhere('gold_color', 'like', "%{$search}%")
                ->orWhere('stones', 'like', "%{$search}%")
                ->orWhere('metal_type', 'like', "%{$search}%")
                ->orWhere('metal_purity', 'like', "%{$search}%")
                ->orWhere('source', 'like', "%{$search}%");
        })
        ->orderBy($sort, $direction)
        ->paginate(20);

        return view('shops.index', compact('goldItems'));
    }



public function transferToBranch(Request $request, string $id)
{
    $validated = $request->validate([
        'shop_id' => 'required|exists:shops,id',
    ]);

    $goldItem = GoldItem::findOrFail($id);
    $goldItem->shop_id = $validated['shop_id'];
    $shop = Shop::findOrFail($validated['shop_id']);
    $goldItem->shop_name = $shop->name;
    $goldItem->save();

    return redirect()->route('gold-items.index')->with('success', 'Gold item transferred successfully.');
}
}
