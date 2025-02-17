<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\SellRequest;
use App\Http\Requests\TransferRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use App\Models\GoldItem;
use App\Models\Shop;
use App\Models\GoldItemSold;
use App\Models\GoldPrice;
use App\Models\Customer;
use App\Models\ItemRequest;
use App\Models\Outer;
use App\Models\Warehouse;
use App\Services\SortAndFilterService;
use App\Services\TransferService;
use App\Services\GoldItemService;
use App\Services\SellService;
use App\Services\OuterService;
use App\Services\WarehouseService;
use Illuminate\Support\Facades\DB;



use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Notifications\TransferRequestNotification;
use Illuminate\Support\Facades\Notification;

class ShopsController extends Controller
{
    private TransferService $transferService;
    private GoldItemService $goldItemService;
    private OuterService $outerService;
    private SellService $saleService;
    private WarehouseService $warehouseService;

    public function __construct(
        TransferService $transferService,
        GoldItemService $goldItemService,
        OuterService $outerService,
        SellService $saleService,
        WarehouseService $warehouseService
    ) {
        $this->transferService = $transferService;
        $this->goldItemService = $goldItemService;
        $this->outerService = $outerService;
        $this->saleService = $saleService;
        $this->warehouseService = $warehouseService;
    }
    public function handleTransferRequest(Request $request, $requestId)
    {
        $validated = $request->validate([
            'status' => 'required|in:accepted,rejected'
        ]);
    
        try {
            $this->transferService->handleTransfer($requestId, $validated['status']);
            return redirect()->back()->with('success', 'Transfer request status updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update transfer request: ' . $e->getMessage());
        }
    }
    public function showShopItems(Request $request)
    {

        $items = $this->goldItemService->getShopItems($request);

        return view('shops.Gold.index', $items);
    }

    public function viewTransferRequests()
    {
        $transferData = $this->transferService->getPendingTransfers();
        return view('shops.transfer_requests.show_requests', [
            'incomingRequests' => $transferData['incomingRequests'],
            'outgoingRequests' => $transferData['outgoingRequests']
        ]);
    }

    public function viewTransferRequestHistory()
    {
        $history = $this->transferService->getTransferHistory();
        return view('shops.transfer_requests.history', compact('history'));
    }


    public function edit(string $id)
    {
        $data = $this->goldItemService->getEditFormData($id);
        return view('Shops.Gold.sell_form', $data);
    }

    public function storeOuter(Request $request)
    {
        $this->outerService->createOuter($request->validated());
        return redirect()->back()->with('status', 'Data saved successfully.');
    }

    public function returnOuter($serialNumber)
    {
        $this->outerService->markAsReturned($serialNumber);
        return redirect()->back()->with('status', 'Item marked as returned.');
    }

    public function toggleReturn($serialNumber)
    {
        $result = $this->outerService->toggleOuterStatus($serialNumber);

        if ($result['redirect']) {
            return view('Shops.Gold.outerform', ['serial_number' => $serialNumber]);
        }

        return redirect()->back()->with($result['status'], $result['message']);
    }

    public function showBulkSellForm(Request $request)
{
    $itemIds = explode(',', $request->input('ids'));
    $data = $this->saleService->getBulkSellFormData($itemIds);
    session()->flash('clear_selections', true);
    return view('shops.Gold.sell_form', $data);
}
    public function bulkSell(SellRequest  $request)
    {
        $this->saleService->processBulkSale($request->validated());
        session()->flash('clear_selections', true);
        return redirect()->route('gold-items.shop')
            ->with('success', 'Selected items sold successfully');
    }

    public function showBulkTransferForm(Request $request)
    {
        $itemIds = explode(',', $request->input('ids'));
        $data = $this->transferService->getBulkTransferFormData($itemIds);
    
        if ($data['goldItems']->isEmpty()) {
            return redirect()->back()
                ->with('error', 'No valid items available for transfer. Some items might already be in transfer process.');
        }
    
        return view('shops.transfer_requests.transfer_form', $data);
    }
    public function bulkTransfer(TransferRequest $request)
    {

        try {
            $itemIds = $request->input('item_ids');
            $this->transferService->bulkTransfer($itemIds, $request->shop_name);

            session()->flash('cleared_items', $itemIds);

            return redirect()->route('dashboard')
                ->with('success', 'Transfer requests sent successfully. Items will be transferred after acceptance.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to create transfer requests: ' . $e->getMessage());
        }
    }

    public function showAdminRequests()
    {
        $requests = ItemRequest::where('shop_name', Auth::user()->shop_name)
            ->with(['item', 'admin'])
            ->latest()
            ->paginate(10);

        return view('shops.admin_requests', compact('requests'));
    }
    public function updateAdminRequests(Request $request, ItemRequest $itemRequest)
    {
        $validated = $request->validate([
            'status' => 'required|in:accepted,rejected'
        ]);

        try {
            DB::transaction(function () use ($validated, $itemRequest) {
                $itemRequest->update(['status' => $validated['status']]);

                if ($validated['status'] === 'accepted') {
                    $goldItem = $itemRequest->item;

                    $goldItem->update([
                        'shop_name' => 'admin',
                        'status' => 'accepted'
                    ]);

                    // Create warehouse record
                    Warehouse::create([
                        'link' => $goldItem->link,
                        'serial_number' => $goldItem->serial_number,
                        'shop_name' => 'admin',
                        'shop_id' => $goldItem->shop_id,
                        'kind' => $goldItem->kind,
                        'model' => $goldItem->model,
                        'talab' => $goldItem->talab,
                        'gold_color' => $goldItem->gold_color,
                        'stones' => $goldItem->stones,
                        'metal_type' => $goldItem->metal_type,
                        'metal_purity' => $goldItem->metal_purity,
                        'quantity' => $goldItem->quantity,
                        'weight' => $goldItem->weight,
                        'rest_since' => $goldItem->rest_since,
                        'source' => $goldItem->source,
                        'to_print' => $goldItem->to_print,
                        'price' => $goldItem->price,
                        'semi_or_no' => $goldItem->semi_or_no,
                        'average_of_stones' => $goldItem->average_of_stones,
                        'net_weight' => $goldItem->net_weight,
                        'website' => $goldItem->website
                    ]);
                }
            });

            return redirect()->back()->with('success', 'Request status updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to process request: ' . $e->getMessage());
        }
    }
    public function getItemsByModel(Request $request)
    {
        $model = $request->input('model');

        // Fetch items with the same model, excluding the current shop
        $items = GoldItem::with('shop')
            ->where('model', $model)
            ->whereHas('shop') // Ensure the item belongs to a shop
            ->get()
            ->map(function ($item) {
                return [
                    'serial_number' => $item->serial_number,
                    'shop_name' => $item->shop->name,
                    'weight' => $item->weight,
                ];
            });

        return response()->json(['items' => $items]);
    }
    public function getItemDetails($serial_number)
    {
        $item = GoldItem::where('serial_number', $serial_number)->first();
        if (!$item) {
            $item = GoldItemSold::where('serial_number', $serial_number)->first();
        }
        return response()->json($item);
    }
}
