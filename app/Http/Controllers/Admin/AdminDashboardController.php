<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ShopWeightAnalysisService;
use App\Services\PopularModelsService;
use Illuminate\View\View;
use App\Models\GoldItemSold;
use App\Models\GoldItem;
use App\Models\Models;
use Illuminate\Http\Request;
// use App\Http\Requests\GoldItemRequest;
use App\Http\Requests\UpdateGoldItemRequest;
use App\Models\DeletedItemHistory;
use App\Services\Admin_GoldItemService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class AdminDashboardController extends Controller
{
    protected $goldItemService;
    // protected $goldItemSoldService;

    protected $shopWeightAnalysisService;
    protected $popularModelsService;

    public function __construct(
        Admin_GoldItemService $goldItemService,

        ShopWeightAnalysisService $shopWeightAnalysisService,
        PopularModelsService $popularModelsService
    ) {
        $this->goldItemService = $goldItemService;
        $this->shopWeightAnalysisService = $shopWeightAnalysisService;
        $this->popularModelsService = $popularModelsService;
    }

    public function dashboard(): View
    {
        $totalWeightSoldByYearAndShop = $this->shopWeightAnalysisService->getTotalWeightSoldByYearAndShop();
        $totalWeightInventory = $this->shopWeightAnalysisService->getTotalWeightInventory();
        $salesTrends = $this->shopWeightAnalysisService->getSalesTrends();
        $topSellingItems = $this->popularModelsService->getPopularModels();
        $inventoryTurnover = $this->shopWeightAnalysisService->getInventoryTurnover();

        // New analysis data
        // Get sales analysis for all kinds
        $kindSalesAnalysis = DB::table('gold_items_sold')
            ->select(
                'kind',
                DB::raw('COUNT(*) as total_sold'),
                DB::raw('SUM(weight) as total_weight')
            )
            ->groupBy('kind')
            ->orderBy('total_sold', 'desc')
            ->get();
        $kindInventory = GoldItem::select('kind', DB::raw('SUM(weight) as total_weight'), DB::raw('COUNT(*) as total_items'))
            ->groupBy('kind')
            ->get();

        $kindSalesTrends = [];
        foreach ($kindSalesAnalysis as $kind) {
            $kindSalesTrends[$kind->kind] = GoldItem::salesTrendByKind($kind->kind)->get();
        }

        // Add new analysis methods
        $monthlyTrends = $this->shopWeightAnalysisService->getMonthlyTrends();
        $turnoverRates = $this->shopWeightAnalysisService->getInventoryTurnoverRates();
        $topPerformers = $this->popularModelsService->getTopPerformers();

        return view('admin.new-dashboard', [
            'salesTrends' => $salesTrends,
            'topSellingItems' => $topSellingItems,
            'inventoryTurnover' => $inventoryTurnover,
            'totalWeightSoldByYearAndShop' => $totalWeightSoldByYearAndShop,
            'totalWeightInventory' => $totalWeightInventory,
            'kindSalesAnalysis' => $kindSalesAnalysis,
            'kindInventory' => $kindInventory,
            'kindSalesTrends' => $kindSalesTrends,
            'monthlyTrends' => $monthlyTrends,
            'turnoverRates' => $turnoverRates,
            'topPerformers' => $topPerformers
        ]);
    }
    public function index(Request $request)
    {
        $goldItems = $this->goldItemService->getGoldItems($request);

        return view('Admin.Gold.Items.Inventory_list', [
            'goldItems' => $goldItems,
        ]);
    }


    public function Sold(Request $request)
    {
        $goldItems = $this->goldItemService->getGoldItemsSold($request);
        $goldPounds = $this->goldItemService->getGoldPoundsSold($request);

        return view('Admin.Gold.sold_index', [
            'goldItems' => $goldItems,
            'goldPounds' => $goldPounds,
        ]);
    }

    public function update(UpdateGoldItemRequest $request, $id)
    {
        $this->goldItemService->updateGoldItem($request, $id);
        return redirect()->route('admin.inventory')->with('success', 'Item updated successfully');
    }
    public function showUpdatedItems() {}

    public function bulkAction(Request $request)
    {
        if (!$request->has('selected_items')) {
            return redirect()->back()->with('error', 'No items selected');
        }

        $action = $request->input('action');
        $selectedItems = $request->input('selected_items');

        try {
            switch ($action) {
                case 'delete':
                    Log::info('Creating deletion history record', [
                        'item_id' => $request->id,
                        'serial_number' => $request->serial_number
                    ]);
                    $reason = $request->input('deletion_reason');
                    $this->goldItemService->bulkDelete($selectedItems, $reason);
                    $message = 'Selected items deleted successfully';
                    break;
                case 'request':
                    $this->goldItemService->bulkRequest($selectedItems);
                    $message = 'Selected items requested successfully';
                    break;
                case 'workshop':
                    $reason = $request->input('transfer_reason');
                    $transferAllModels = $request->input('transfer_all_models') === 'true';

                    // Prepare items array with required data
                    $items = [];
                    foreach ($selectedItems as $id) {
                        $item = GoldItem::find($id);
                        if ($item) {
                            $items[] = [
                                'id' => $item->id,
                                'serial_number' => $item->serial_number,
                                'shop_name' => $item->shop_name
                            ];
                        }
                    }

                    // First create workshop requests
                    $this->goldItemService->createWorkshopRequests(
                        $items,
                        $reason,
                        $transferAllModels
                    );

                    // Then perform the actual transfer
                    $this->goldItemService->bulkTransferToWorkshop(
                        $selectedItems,
                        $reason,
                        $transferAllModels
                    );

                    $message = $transferAllModels
                        ? 'All items with matching models transferred to workshop successfully'
                        : 'Selected items transferred to workshop successfully';
                    break;
                default:
                    return redirect()->back()->with('error', 'Invalid action');
            }

            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            Log::error('Bulk action failed', [
                'action' => $action,
                'items' => $selectedItems,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function deletedItems(Request $request)
    {
        $deletedItems = DeletedItemHistory::all();

        return view('admin.Gold.deleted_items_history', compact('deletedItems'));
    }

    public function workshopItems(Request $request)
    {
        $workshopItems = DB::table('workshop_items')
            ->orderBy('transferred_at', 'desc')
            ->paginate(20);

        return view('admin.Gold.workshop_items', compact('workshopItems'));
    }

    public function workshopRequests(Request $request)
    {
        $query = DB::table('workshop_transfer_requests');
        
        // Apply filters
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        } else {
            // Default to pending if no status is specified
            $query->where('status', 'pending');
        }
        
        if ($request->has('shop_name') && $request->shop_name != '') {
            $query->where('shop_name', $request->shop_name);
        }
        
        if ($request->has('date') && $request->date != '') {
            $query->whereDate('created_at', $request->date);
        }
        
        // Get all shop names for the filter dropdown
        $shops = DB::table('workshop_transfer_requests')
            ->select('shop_name')
            ->distinct()
            ->pluck('shop_name');
            
        $requests = $query->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.requests.workshop_requests', compact('requests', 'shops'));
    }

    public function shopWorkshopRequests(Request $request)
    {
        // Get the authenticated user's shop name
        $shopName = Auth::user()->shop_name;
        
        // Debug info - let's log what we're looking for
        Log::info('Shop workshop requests being checked for shop: ' . $shopName);
        
        // First, check if there are any requests for this exact shop name
        $exactMatchCount = DB::table('workshop_transfer_requests')
            ->where('shop_name', $shopName)
            ->count();
            
        Log::info('Found ' . $exactMatchCount . ' exact matches for shop name: ' . $shopName);
        
        // Get all unique shop names in the system for comparison
        $allShopNames = DB::table('workshop_transfer_requests')
            ->select('shop_name')
            ->distinct()
            ->pluck('shop_name')
            ->toArray();
            
        Log::info('All shop names in workshop_transfer_requests: ' . implode(', ', $allShopNames));
        
        // Query with the authenticated user's shop name
        $query = DB::table('workshop_transfer_requests')
            ->where('shop_name', $shopName);
        
        // Apply filters
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
            
        // Log the count of requests found
        Log::info('Found ' . $requests->total() . ' requests for shop: ' . $shopName);
        
        // For debugging purposes, if no requests are found with the exact name,
        // show a message to the user about possible shop name mismatch
        $allShops = $allShopNames;
        $noRequestsFound = $requests->total() === 0;

        return view('Shops.workshop_requests', compact('requests', 'allShops', 'noRequestsFound', 'shopName'));
    }

    public function handleShopWorkshopRequests(Request $request)
    {
        try {
            if (!$request->has('selected_items') || empty($request->selected_items)) {
                return redirect()->back()->with('error', 'No items selected');
            }
            
            DB::beginTransaction();
            
            $shopName = Auth::user()->shop_name;
            $action = $request->input('action');
            $selectedItems = $request->input('selected_items');
            
            foreach ($selectedItems as $id) {
                $transferRequest = DB::table('workshop_transfer_requests')
                    ->where('id', $id)
                    ->where('shop_name', $shopName) // Ensure the request belongs to this shop
                    ->first();
                
                if (!$transferRequest) {
                    continue; // Skip if request not found or not belonging to shop
                }
                
                // Update request status based on action
                $status = ($action === 'accept') ? 'accepted_by_shop' : 'rejected_by_shop';
                
                DB::table('workshop_transfer_requests')
                    ->where('id', $id)
                    ->update([
                        'status' => $status,
                        'updated_at' => now()
                    ]);
                
                // Get the gold item
                $goldItem = GoldItem::find($transferRequest->item_id);
                if ($goldItem) {
                    if ($action === 'accept') {
                        // When accepted, mark item as pending for workshop
                        $goldItem->status = 'pending_workshop';
                        $goldItem->save();
                    } else {
                        // If rejected, reset the status back to normal
                        $goldItem->status = null;
                        $goldItem->save();
                    }
                }
            }
            
            DB::commit();
            
            $message = ($action === 'accept') 
                ? 'Selected workshop requests accepted successfully' 
                : 'Selected workshop requests rejected successfully';
                
            return redirect()->route('shop.workshop.requests')->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to handle workshop requests: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function handleWorkshopRequest(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $transferRequest = DB::table('workshop_transfer_requests')
                ->where('id', $id)
                ->first();

            if (!$transferRequest) {
                throw new \Exception('Transfer request not found');
            }

            // Check if the request has been previously accepted by the shop
            $wasAcceptedByShop = $transferRequest->status === 'accepted_by_shop';
            
            // Get the gold item
            $goldItem = GoldItem::find($transferRequest->item_id);
            
            if (!$goldItem) {
                throw new \Exception('Gold item not found. It may have been deleted already.');
            }

            // Handle based on the action
            if ($request->status === 'approved') {
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

                // No need to update shop_name to 'rabea' anymore
                // Keep the original shop name
                
                // Delete the item from gold_items table
                $goldItem->delete();
            } 
            else if ($request->status === 'return_to_shop') {
                // Return the item to the shop, remove the pending_kasr status
                $goldItem->status = null;
                $goldItem->save();
            }
            else if ($request->status === 'rejected') {
                // Reset the item status
                $goldItem->status = null;
                $goldItem->save();
            }

            // Update request status
            DB::table('workshop_transfer_requests')
                ->where('id', $id)
                ->update([
                    'status' => $request->status,
                    'updated_at' => now()
                ]);

            DB::commit();
            
            $successMessage = $wasAcceptedByShop ? 
                'Workshop request from shop updated successfully' : 
                'Workshop request updated successfully';
                
            return redirect()->route('admin.inventory')->with('success', $successMessage);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Workshop request handling failed: ' . $e->getMessage());
            throw $e;
        }
    }
    public function update_prices()
    {
        return view('shopify.update_price');
    }

    public function createWorkshopRequests(Request $request)
    {
        try {
            $request->validate([
                'items' => 'required|string', // JSON string of item IDs
                'transfer_reason' => 'required|string',
                'transfer_all_models' => 'nullable|string'
            ]);

            DB::beginTransaction(); // Start transaction

            // Decode the JSON items
            $items = json_decode($request->input('items'), true);
            $reason = $request->input('transfer_reason');
            $transferAllModels = $request->input('transfer_all_models') === 'true';

            if ($transferAllModels) {
                // Extract all unique models from the selected items
                $models = [];
                foreach ($items as $item) {
                    $goldItem = GoldItem::findOrFail($item['id']);
                    $models[] = $goldItem->model;
                }
                $models = array_unique($models);

                // Get all items with these models
                $allItemsWithModels = GoldItem::whereIn('model', $models)->get();
                
                // Create workshop requests for all items with these models
                foreach ($allItemsWithModels as $goldItem) {
                    DB::table('workshop_transfer_requests')->insert([
                        'item_id' => $goldItem->id,
                        'shop_name' => $goldItem->shop_name,  // Use the original shop name
                        'serial_number' => $goldItem->serial_number,
                        'status' => 'pending',
                        'reason' => $reason,
                        'requested_by' => Auth::user()->shop_name,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    
                    // Update gold item status to pending_kasr
                    $goldItem->status = 'pending_kasr';
                    $goldItem->save();
                }
            } else {
                // Process only selected items
                foreach ($items as $item) {
                    // Make sure we're using the 'id' field from the JSON object
                    $itemId = $item['id'];
                    $goldItem = GoldItem::findOrFail($itemId);
                    
                    // Create workshop transfer request
                    DB::table('workshop_transfer_requests')->insert([
                        'item_id' => $goldItem->id,
                        'shop_name' => $goldItem->shop_name,  // Use the original shop name
                        'serial_number' => $goldItem->serial_number,
                        'status' => 'pending',
                        'reason' => $reason,
                        'requested_by' => Auth::user()->shop_name,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    
                    // Update gold item status to pending_kasr
                    $goldItem->status = 'pending_kasr';
                    $goldItem->save();
                }
            }

            DB::commit();

            return redirect()->route('admin.inventory')->with('success', 'Workshop transfer requests created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create workshop requests: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to create workshop requests: ' . $e->getMessage());
        }
    }

    public function shopNameMatching()
    {
        // Get all users that might be shops
        $shopUsers = DB::table('users')
            ->whereNotIn('name', ['Admin', 'admin', 'Administrator', 'administrator', 'Rabea'])
            ->get(['id', 'name', 'email' ,'shop_name']);
            
        // Get all distinct shop names from gold_items
        $goldItemShops = DB::table('gold_items')
            ->select('shop_name')
            ->distinct()
            ->whereNotNull('shop_name')
            ->pluck('shop_name')
            ->toArray();
            
        // Get all shop names from workshop_transfer_requests
        $workshopRequestShops = DB::table('workshop_transfer_requests')
            ->select('shop_name')
            ->distinct()
            ->pluck('shop_name')
            ->toArray();
            
        // Combine all unique shop names
        $allShopNames = array_unique(array_merge($goldItemShops, $workshopRequestShops));
        
        return view('admin.shop_name_matching', compact('shopUsers', 'allShopNames'));
    }
    
    public function updateShopName(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'shop_name' => 'required|string'
        ]);
        
        try {
            DB::table('users')
                ->where('id', $request->user_id)
                ->update(['shop_name' => $request->shop_name]);
                
            return redirect()->back()->with('success', 'Shop name updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update shop name: ' . $e->getMessage());
        }
    }
}
 // public function allSoldItems()
    // {
    //     $goldItems = $this->goldItemSoldService->getAllSoldItems();

    //     return view('admin.sold-items', [
    //         'goldItems' => $goldItems,
    //     ]);
    // }
    
      // public function models_index(Request $request)
    // {
    //     $goldItems = $this->goldItemService->getGoldItems($request);
    //     $models = Models::with(['goldItems', 'goldItemsAvg'])->get();

    //     return view('admin.Gold.Models.models', compact('models','goldItems'));
    // }
//     public function edit($id)
// {
//     $goldItem = $this->goldItemService->findGoldItem($id);
//     return view('Admin.Gold.edit', compact('goldItem'));
// }