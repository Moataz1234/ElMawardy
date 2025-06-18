<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GoldItem;
use App\Models\Customer;
use App\Models\ItemRequest;
use App\Models\Warehouse;
use App\Models\SaleRequest;
use App\Services\GoldItemService;
use App\Services\SellService;
use App\Services\TransferService;
use App\Services\OuterService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\SellRequest;
use App\Http\Requests\TransferRequest;

class ShopController extends Controller
{
    private GoldItemService $goldItemService;
    private SellService $saleService;
    private TransferService $transferService;
    private OuterService $outerService;

    public function __construct(
        GoldItemService $goldItemService,
        SellService $saleService,
        TransferService $transferService,
        OuterService $outerService
    ) {
        $this->goldItemService = $goldItemService;
        $this->saleService = $saleService;
        $this->transferService = $transferService;
        $this->outerService = $outerService;
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

    public function bulkSell(SellRequest $request)
    {
        $validated = $request->validated();
        $validated['pound_prices'] = $request->input('pound_prices', []);

        $customer = Customer::where('phone_number', $validated['phone_number'])->first();

        if (!$customer) {
            $customer = Customer::create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'address' => $validated['address'],
                'phone_number' => $validated['phone_number'],
                'email' => $validated['email'],
                'payment_method' => $validated['payment_method'],
            ]);
        }

        $validated['customer_id'] = $customer->id;

        $result = $this->saleService->processBulkSale($validated);
        session()->flash('clear_selections', true);
        
        return response()->json([
            'success' => true,
            'message' => 'Selected items sold successfully',
            'data' => $result['data']
        ]);
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

                    Warehouse::create([
                        'model' => $goldItem->model,
                        'serial_number' => $goldItem->serial_number,
                        'kind' => $goldItem->kind,
                        'weight' => $goldItem->weight,
                        'gold_color' => $goldItem->gold_color,
                        'metal_type' => $goldItem->metal_type,
                        'metal_purity' => $goldItem->metal_purity,
                        'quantity' => $goldItem->quantity,
                        'stones' => $goldItem->stones,
                        'talab' => $goldItem->talab,
                    ]);
                }
            });

            return redirect()->back()->with('success', 'Request status updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to process request: ' . $e->getMessage());
        }
    }

    public function getCustomerData(Request $request)
    {
        $phoneNumber = $request->query('phone_number');
        $customer = Customer::where('phone_number', $phoneNumber)->first();

        if ($customer) {
            $purchaseHistory = SaleRequest::where('customer_id', $customer->id)
                ->with(['goldItem'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($request) {
                    $request->status_badge = match($request->status) {
                        'approved' => 'bg-success',
                        'pending' => 'bg-warning',
                        'rejected' => 'bg-danger',
                        default => 'bg-secondary'
                    };
                    return $request;
                });

            $lastTransaction = $purchaseHistory
                ->where('status', 'approved')
                ->first();

            if ($lastTransaction) {
                $customer->payment_method = $lastTransaction->payment_method ?? $customer->payment_method;
            }

            return response()->json([
                'success' => true,
                'customer' => $customer,
                'isReturningCustomer' => $purchaseHistory->isNotEmpty(),
                'purchaseHistory' => $purchaseHistory
            ]);
        }

        return response()->json(['success' => false]);
    }
} 