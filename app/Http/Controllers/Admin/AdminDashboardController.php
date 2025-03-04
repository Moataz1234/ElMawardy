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

        return view('Admin.Gold.sold_index', [
            'goldItems' => $goldItems,
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
        $requests = DB::table('workshop_transfer_requests')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.requests.workshop_requests', compact('requests'));
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

            if ($request->status === 'approved') {
                // Get the gold item
                $goldItem = GoldItem::findOrFail($transferRequest->item_id);

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

                // Delete the original item
                $goldItem->delete();
            }

            // Update request status
            DB::table('workshop_transfer_requests')
                ->where('id', $id)
                ->update([
                    'status' => $request->status,
                    'updated_at' => now()
                ]);

            DB::commit();
            return true;
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
                'transfer_reason' => 'required|string'
            ]);

            DB::beginTransaction(); // Start transaction

            // Decode the JSON items
            $items = json_decode($request->input('items'), true);
            $reason = $request->input('transfer_reason');

            foreach ($items as $itemId) {
                $goldItem = GoldItem::findOrFail($itemId);
                
                // Create workshop transfer request
                DB::table('workshop_transfer_requests')->insert([
                    'item_id' => $itemId,
                    'shop_name' => $goldItem->shop_name,
                    'serial_number' => $goldItem->serial_number,
                    'status' => 'pending',
                    'reason' => $reason,
                    'requested_by' => Auth::user()->name,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Workshop transfer requests created successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create workshop requests: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create workshop requests: ' . $e->getMessage()
            ], 500);
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