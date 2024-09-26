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

    $fromShop = Shop::findOrFail($goldItem->shop_id);
    $toShop = Shop::where('name', $request->input('shop_name'))->firstOrFail();

    TransferRequest::create([
        'gold_item_id' => $goldItem->id,
        'from_shop_name' => $fromShop->name,
        'to_shop_name' => $toShop->name,
        'status' => 'pending'
    ]);

    return redirect()->back()->with('message', 'Transfer request sent to the shop.');
}

public function handleTransferRequest($id, $status)
{
    $transferRequest = TransferRequest::findOrFail($id);

    if ($status === 'accepted') {
        $goldItem = $transferRequest->goldItem;
        $goldItem->shop_id = $transferRequest->to_shop_id;
        $goldItem->save();
    }

    $transferRequest->status = $status;
    $transferRequest->save();

    return redirect()->route('transfer.requests')->with('message', 'Transfer request has been ' . $status);
}

public function viewTransferRequests()
{
    $user = Auth::user();
    $shopName = $user->name;

    $transferRequests = TransferRequest::with(['goldItem', 'fromShop', 'toShop'])
                            ->where('to_shop_name', $shopName)
                            ->where('status', 'pending')
                            ->get();

    return view('shops.transfer_requests.index', compact('transferRequests'));
}

public function showTransferForm(string $id)
{
    $goldItem = GoldItem::findOrFail($id);
    $shops = Shop::all();
    return view('admin.Gold.Gold_transfer', compact('goldItem', 'shops'));
}
public function showShopItems(Request $request)
{
    $user = Auth::user();
    $search = $request->input('search');
    $sort = $request->input('sort', 'serial_number');
    $direction = $request->input('direction', 'asc');

    $goldItems = GoldItem::where('shop_name', $user->name)
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
