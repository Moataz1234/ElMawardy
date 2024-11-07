<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\GoldItem;
use App\Models\Shop;
use App\Models\TransferRequest;
use App\Models\GoldItemSold;
use App\Models\GoldPrice;
use App\Models\Customer;
use App\Models\Outer;
use App\Services\SortAndFilterService;

use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Notifications\TransferRequestNotification;
use Illuminate\Support\Facades\Notification;

class ShopsController extends Controller
{
    protected $sortAndFilterService;
    public function __construct(SortAndFilterService $sortAndFilterService)
    {
        $this->sortAndFilterService = $sortAndFilterService;
    }
    public function transferRequest(Request $request, $id)
    {
    $goldItem = GoldItem::findOrFail($id);

    $toShop = Shop::findOrFail($request->input('shop_id'));

    TransferRequest::create([
        'gold_item_id' => $goldItem->id,
        'from_shop_id' => $goldItem->shop_id,
        'to_shop_id' => $toShop->id,
        'status' => 'pending'
    ]);

    return redirect()->back()->with('message', 'Transfer request sent to the shop.');
    }


    public function viewTransferRequestHistory()
    {
        $transferRequests = TransferRequest::with(['goldItem', 'fromShop', 'toShop'])->get();

        return view('shops.transfer_requests.history', compact('transferRequests'));
    }

    public function handleTransferRequest($id, $status)
    {
        $transferRequest = TransferRequest::findOrFail($id);

        if ($status === 'accepted') {
            $goldItem = $transferRequest->goldItem;
            $goldItem->shop_id = $transferRequest->to_shop_id;
            $goldItem->shop_name = $transferRequest->toShop->name;
            $goldItem->save();
        }

        $transferRequest->status = $status;
        $transferRequest->save();

        return redirect()->route('transfer.requests')->with('message', 'Transfer request has been ' . $status);
    }

    public function viewTransferRequests()
    {
        $user = Auth::user();
        $shopId = $user->id;
        $transferRequests = TransferRequest::with(['goldItem', 'fromShop', 'toShop'])
                                ->where('to_shop_id', $shopId)
                                ->where('status', 'pending')
                                ->get();

        return view('shops.transfer_requests.index', compact('transferRequests'));
    }

    public function showTransferForm(string $id)
    {
        $goldItem = GoldItem::findOrFail($id);
        $shops = Shop::all();
        return view('shops.transfer_requests.transfer_form', compact('goldItem', 'shops'));
    }

    public function showShopItems(Request $request)
    {
        $user = Auth::user();
        // $search = $request->input('search');
        [$sortColumn, $sortDirection] = $this->sortAndFilterService->getSortColumnAndDirection($request->get('sort'));

        $query = GoldItem::query();
        $query = GoldItem::where('shop_name', $user->shop_name);

        
        // $sort = $request->input('sort', 'serial_number');
        // $direction = $request->input('direction', 'asc');

        $latestPrices = GoldPrice::latest()->take(1)->get();  
        $latestGoldPrice = GoldPrice::orderBy('id', 'desc')->first();
        $goldWithWork = $latestGoldPrice ? $latestGoldPrice->gold_with_work : 0;
        $goldItems = GoldItem::with('modelCategory')->get();
        $goldItems = GoldItem::with('modelCategory.categoryPrice')->get();


        foreach ($goldItems as $item) {
            if ($item->weight && $goldWithWork) {
                // Initial calculated price
                $calculatedPrice = $item->weight * $goldWithWork;
    
                // Check the model category and apply adjustments
                if ($item->modelCategory) {
                    switch ($item->modelCategory->category) {
                        case '*':
                            // No change to calculatedPrice
                            break;
                        case '**':
                            $calculatedPrice -= 200* $item->weight;
                            break;
                        case '***':
                            $calculatedPrice -= 400 *$item->weight;
                            break;
                    }
                }
    
                // Assign the calculated price to the item
                $item->calculated_price = max(0, $calculatedPrice); // Prevent negative prices
            } else {
                $item->calculated_price = 0; // Default if weight or gold price is missing
            }
            
        }
        if ($request->filled('metal_purity')) {
            $query->whereIn('metal_purity', $request->get('metal_purity'));
        }
        if ($request->filled('gold_color')) {
            $query->whereIn('gold_color', $request->input('gold_color'));
        }
        // Apply kind filter if selected
        if ($request->filled('kind')) {
            $query->whereIn('kind', $request->get('kind'));
        }
        $golditems = $query->orderBy($sortColumn, $sortDirection)->paginate(36);

        $goldItems->appends($request->all());

        return view('shops.Gold.index', compact('goldItems','latestPrices','latestGoldPrice'));
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
    public function edit(string $id)
    {
        $goldItem = GoldItem::findOrFail($id);
        $shops = Shop::all(); // Assuming you have a Shop model
        return view('Shops.Gold.sell_form', compact('goldItem', 'shops'));
    }
    public function storeOuter(Request $request)
{
    $outer = Outer::create([
        'first_name' => $request->input('first_name'),
        'last_name' => $request->input('last_name'),
        'phone_number' => $request->input('phone_number'),
        'reason' => $request->input('reason'),
        'gold_serial_number' => $request->input('gold_serial_number'),
        'is_returned' => false,
    ]);

    return redirect()->back()->with('status', 'Data saved successfully.');
}
public function returnOuter($serialNumber)
{
    $outer = Outer::where('gold_serial_number', $serialNumber)->first();
    if ($outer) {
        $outer->is_returned = true;
        $outer->save();
    }

    return redirect()->back()->with('status', 'Item marked as returned.');
}
public function toggleReturn($serial_number)
{
    // Find the item by serial number
    $item = GoldItem::where('serial_number', $serial_number)->first();
    
    // Check if the item exists
    if ($item) {
        $outer = Outer::where('gold_serial_number', $serial_number)->first();
        
        if ($outer) {
            // Toggle the is_returned value
            $outer->is_returned = false; // Change it back to false
            $outer->save();
            return redirect()->back()->with('success', 'Item status updated to Outer.');
        } else {
            // Redirect to a form for creating a new outer entry
            return view('Shops.Gold.outerform', compact('serial_number'));
        }
    }

    return redirect()->back()->with('error', 'Item not found.');
}
public function showBulkSellForm(Request $request)
{
    $ids = explode(',', $request->input('ids'));
    $goldItems = GoldItem::whereIn('id', $ids)->get();
    session()->flash('clear_selections', true);

    return view('shops.Gold.sell_form', compact('goldItems'));
}

public function showBulkTransferForm(Request $request)
{
    $ids = explode(',', $request->input('ids'));
    $goldItems = GoldItem::whereIn('id', $ids)->get();
    $shops = Shop::all(); // Assuming you have a Shop model to fetch all shops

    return view('shops.Gold.bulk_transfer_form', compact('goldItems', 'shops'));
}
public function bulkSell(Request $request)
{
    $validated=$request->validate([
        'ids' => 'required|array',
        'first_name' => 'required|string',
        'last_name' => 'required|string',
        'phone_number' => 'required',
        'address' => 'required|string|max:255',
        'email' => 'required|email',
        'payment_method' => 'required'
    ]);

    // Handle the selling logic here for each item
    foreach ($request->ids as $id) {
        $goldItem = GoldItem::findOrFail($id);
        
        // Create GoldItemSold and assign customer details, then delete GoldItem entry
        // GoldItemSold::create(array_merge($goldItem->toArray(), [
        //     'customer_name' => $request->first_name . ' ' . $request->last_name,
        //     'sold_date' => now()
        // ]));
        $customer = Customer::create($validated);
    
        $goldItem = GoldItem::findOrFail($id);

        // Set customer_id in GoldItem

        // Transfer data to GoldItemSold
        $goldItemSold = GoldItemSold::create($goldItem->toArray());
        $goldItemSold->customer_id = $customer->id;
        $goldItemSold->sold_date = now();
        $goldItemSold->save();
        $goldItem->delete();
    }
    echo "<script>localStorage.removeItem('selectedItems');</script>";
    session()->flash('clear_selections', true);

    return redirect()->route('gold-items.shop')->with('success', 'Selected items sold successfully.');
}
}
