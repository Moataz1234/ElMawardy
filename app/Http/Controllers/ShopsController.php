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
use App\Services\Admin_GoldItemService;


use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Notifications\TransferRequestNotification;
use Illuminate\Support\Facades\Notification;
use App\Models\GoldPoundInventory;
use App\Models\SaleRequest;
use App\Models\User;
use App\Models\TransferRequestHistory;

class ShopsController extends Controller
{
    private TransferService $transferService;
    private GoldItemService $goldItemService;
    private Admin_GoldItemService $adminGoldItemService;
    private OuterService $outerService;
    private SellService $saleService;
    private WarehouseService $warehouseService;

    public function __construct(
        TransferService $transferService,
        GoldItemService $goldItemService,
        OuterService $outerService,
        SellService $saleService,
        WarehouseService $warehouseService,
        Admin_GoldItemService $adminGoldItemService
    ) {
        $this->transferService = $transferService;
        $this->goldItemService = $goldItemService;
        $this->outerService = $outerService;
        $this->saleService = $saleService;
        $this->warehouseService = $warehouseService;
        $this->adminGoldItemService = $adminGoldItemService;
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

    // public function viewTransferRequestHistory()
    // {
    //     $transferRequests  = $this->transferService->getTransferHistory();
    //     return view('shops.transfer_requests.history', compact('transferRequests'));
    // }


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

//     public function showBulkSellForm(Request $request)
// {
//     $itemIds = explode(',', $request->input('ids'));
//     $data = $this->saleService->getBulkSellFormData($itemIds);
//     session()->flash('clear_selections', true);
//     return view('shops.Gold.sell_form', $data);
// }
//     public function bulkSell(SellRequest $request)
//     {
//         Log::info('Bulk sell request received', [
//             'request_data' => $request->all()
//         ]);

//         try {
//             $validated = $request->validated();
//             Log::info('Request validation passed', ['validated_data' => $validated]);

//             $result = $this->saleService->processBulkSale($validated);
//             session()->flash('clear_selections', true);
            
//             Log::info('Bulk sell completed successfully', ['result' => $result]);
            
//             return response()->json([
//                 'success' => true,
//                 'message' => 'Selected items sold successfully',
//                 'data' => $result
//             ]);
//         } catch (\Illuminate\Validation\ValidationException $e) {
//             Log::error('Validation error in bulk sell', [
//                 'errors' => $e->errors()
//             ]);
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Validation failed',
//                 'errors' => $e->errors()
//             ], 422);
//         } catch (\Exception $e) {
//             Log::error('Error in bulk sell', [
//                 'error' => $e->getMessage(),
//                 'trace' => $e->getTraceAsString()
//             ]);
            
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Failed to process sale: ' . $e->getMessage()
//             ], 500);
//         }
//     }
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
    $validated['pound_prices'] = $request->input('pound_prices', []); // Add pound prices from form

    // Check if a customer with the given phone number exists
    $customer = Customer::where('phone_number', $validated['phone_number'])->first();

    if (!$customer) {
        // If no customer exists, create a new one
        $customer = Customer::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'address' => $validated['address'],
            'phone_number' => $validated['phone_number'],
            'email' => $validated['email'],
            'payment_method' => $validated['payment_method'],
        ]);
    }

    // Use the existing or newly created customer's ID
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

                    // Create warehouse record
                    Warehouse::create([
                        'model' => $goldItem->model,
                        'serial_number' => $goldItem->serial_number,
                        // 'shop_name' => 'admin',
                        // 'shop_id' => $goldItem->shop_id,
                        'kind' => $goldItem->kind,
                        'weight' => $goldItem->weight,
                        'gold_color' => $goldItem->gold_color,
                        'metal_type' => $goldItem->metal_type,
                        'metal_purity' => $goldItem->metal_purity,
                        'quantity' => $goldItem->quantity,
                        'stones' => $goldItem->stones,
                        'talab' => $goldItem->talab,
                        // 'rest_since' => $goldItem->rest_since,
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
        Log::info('Getting items with model', ['model' => $model]);

        // Fetch items with the same model, including status
        $items = GoldItem::where('model', $model)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'serial_number' => $item->serial_number,
                    'shop_name' => $item->shop_name,
                    'weight' => $item->weight,
                    'status' => $item->status, // Include item status
                    'model' => $item->model,
                    'gold_color' => $item->gold_color
                ];
            });
        
        Log::info('Found items with model', [
            'model' => $model, 
            'count' => $items->count(),
            'items_with_status' => $items->map(function($i) { 
                return ['id' => $i['id'], 'serial' => $i['serial_number'], 'status' => $i['status']]; 
            })
        ]);

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

    public function submitPoundPrice(Request $request)
    {
        try {
            $validated = $request->validate([
                'serial_number' => 'required|string',
                'customer_id' => 'required|exists:customers,id',
                'price' => 'required|numeric|min:0'
            ]);

            DB::transaction(function () use ($validated) {
                // Get the pound inventory record
                $poundInventory = GoldPoundInventory::where('serial_number', $validated['serial_number'])
                    ->firstOrFail();

                // Create sale request for the pound
                SaleRequest::create([
                    'item_serial_number' => $validated['serial_number'],
                    'shop_name' => Auth::user()->shop_name,
                    'status' => 'pending',
                    'customer_id' => $validated['customer_id'],
                    'price' => $validated['price'],
                    'payment_method' => 'cash', // You might want to pass this from the form
                    'item_type' => 'pound',
                    'weight' => $poundInventory->goldPound->weight,
                    'purity' => $poundInventory->goldPound->purity,
                    'kind' => $poundInventory->goldPound->kind
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Pound price submitted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to submit pound price: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit pound price: ' . $e->getMessage()
            ], 500);
        }
    }

    public function checkAssociatedPounds(Request $request)
    {
        Log::info('Checking associated pounds', ['request_data' => $request->all()]); // Debug log

        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:gold_items,id'
        ]);

        foreach ($validated['ids'] as $id) {
            $goldItem = GoldItem::findOrFail($id);
            Log::info('Checking item', ['item_id' => $id, 'serial_number' => $goldItem->serial_number]); // Debug log
            
            $poundInventory = GoldPoundInventory::where('related_item_serial', $goldItem->serial_number)->first();
            
            if ($poundInventory) {
                Log::info('Found associated pound', ['pound_inventory' => $poundInventory->toArray()]); // Debug log
                $pound = $poundInventory->goldPound;
                
                return response()->json([
                    'hasPound' => true,
                    'poundDetails' => [
                        'serial_number' => $poundInventory->serial_number,
                        'kind' => $pound->kind,
                        'weight' => $pound->weight,
                        'purity' => $pound->purity
                    ]
                ]);
            }
        }

        Log::info('No associated pounds found'); // Debug log
        return response()->json(['hasPound' => false]);
    }
    public function getAllItems(Request $request)
    {
        $goldItems = $this->adminGoldItemService->getGoldItems($request);
        return view('Shops.Gold.all_items', compact('goldItems'));
    }

    public function getCustomerData(Request $request)
    {
        $phoneNumber = $request->query('phone_number');
        $customer = Customer::where('phone_number', $phoneNumber)->first();

        if ($customer) {
            // Get all sale requests (both approved and pending) for the customer
            $purchaseHistory = SaleRequest::where('customer_id', $customer->id)
                ->with(['goldItem'])  // Eager load relationships
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($request) {
                    // Add a status badge class for styling
                    $request->status_badge = match($request->status) {
                        'approved' => 'bg-success',
                        'pending' => 'bg-warning',
                        'rejected' => 'bg-danger',
                        default => 'bg-secondary'
                    };
                    return $request;
                });

            // Get the most recent successful transaction for default values
            $lastTransaction = $purchaseHistory
                ->where('status', 'approved')
                ->first();

            // Update customer data from their last approved transaction if available
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

    public function showWorkshopRequests()
    {
        // Check if the user's shop name is 'rabea'
        if (Auth::user()->shop_name !== 'rabea') {
            return redirect()->route('dashboard')
                ->with('error', 'Only Rabea shop can view workshop requests');
        }
        
        $query = DB::table('workshop_transfer_requests')
            ->where('shop_name', 'rabea');
            
        // Apply filters
        $request = request();
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        
        if ($request->has('date') && $request->date != '') {
            $query->whereDate('created_at', $request->date);
        }
        
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('serial_number', 'like', '%' . $search . '%')
                  ->orWhere('reason', 'like', '%' . $search . '%')
                  ->orWhere('requested_by', 'like', '%' . $search . '%');
            });
        }
        
        $requests = $query->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('shops.workshop_requests', compact('requests'));
    }
    
    public function handleWorkshopRequests(Request $request)
    {
        // Check if the user's shop name is 'rabea'
        if (Auth::user()->shop_name !== 'rabea') {
            return redirect()->route('dashboard')
                ->with('error', 'Only Rabea shop can handle workshop requests');
        }
        
        try {
            if (!$request->has('selected_items') || empty($request->selected_items)) {
                return redirect()->back()->with('error', 'No items selected');
            }
            
            DB::beginTransaction();
            
            $action = $request->input('action');
            $selectedItems = $request->input('selected_items');
            
            foreach ($selectedItems as $id) {
                $transferRequest = DB::table('workshop_transfer_requests')
                    ->where('id', $id)
                    ->where('shop_name', 'rabea') // Ensure the request belongs to rabea shop
                    ->first();
                
                if (!$transferRequest) {
                    continue; // Skip if request not found or not belonging to rabea
                }
                
                // Get the gold item
                $goldItem = GoldItem::find($transferRequest->item_id);
                if (!$goldItem) {
                    continue; // Skip if gold item not found
                }
                
                if ($action === 'approve') {
                    // Create workshop record
                    DB::table('workshop_items')->insert([
                        'item_id' => $goldItem->id,
                        'transferred_by' => $transferRequest->requested_by,
                        'serial_number' => $goldItem->serial_number,
                        'shop_name' => $goldItem->shop_name,
                        'kind' => $goldItem->kind,
                        'model' => $goldItem->model,
                        'gold_color' => $goldItem->gold_color,
                        'metal_purity' => $goldItem->metal_purity,
                        'weight' => $goldItem->weight,
                        'transfer_reason' => $transferRequest->reason,
                        'transferred_at' => now(),
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    
                    // Update status and delete the item
                    $goldItem->delete();
                    
                    // Update request status
                    DB::table('workshop_transfer_requests')
                        ->where('id', $id)
                        ->update([
                            'status' => 'approved',
                            'updated_at' => now()
                        ]);
                } 
                else if ($action === 'reject') {
                    // Reset the item status
                    $goldItem->status = null;
                    $goldItem->save();
                    
                    // Update request status
                    DB::table('workshop_transfer_requests')
                        ->where('id', $id)
                        ->update([
                            'status' => 'rejected',
                            'updated_at' => now()
                        ]);
                }
            }
            
            DB::commit();
            
            $message = ($action === 'approve') 
                ? 'Selected items transferred to workshop successfully' 
                : 'Selected workshop requests rejected successfully';
                
            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to handle workshop requests: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Display gold items specifically for Rabea shop
     */
    public function showRabeaItems(Request $request)
    {
        // Ensure the user is from Rabea shop
        // if (Auth::user()->shop_name !== 'Rabea') {
        //     return redirect()->route('dashboard')
        //         ->with('error', 'Only Rabea shop can access this page');
        // }
        
        // Get items for Rabea shop
        $goldItems = GoldItem::where('shop_name', 'Rabea')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('Rabea.items_list', compact('goldItems'));
    }

}
