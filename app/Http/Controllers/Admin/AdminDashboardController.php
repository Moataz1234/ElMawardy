<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ShopWeightAnalysisService;
use App\Services\PopularModelsService;
use Illuminate\View\View;
use App\Models\GoldItemSold;
use Illuminate\Http\Request;
// use App\Http\Requests\GoldItemRequest;
use App\Http\Requests\UpdateGoldItemRequest;
use App\Models\DeletedItemHistory;
use App\Services\Admin_GoldItemService;
use Illuminate\Support\Facades\Log;

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

    // public function allSoldItems()
    // {
    //     $goldItems = $this->goldItemSoldService->getAllSoldItems();

    //     return view('admin.sold-items', [
    //         'goldItems' => $goldItems,
    //     ]);
    // }
    public function index(Request $request)
    {
        $goldItems = $this->goldItemService->getGoldItems($request);

        return view('Admin.Gold.Inventory_list', [
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
    public function edit($id)
{
    $goldItem = $this->goldItemService->findGoldItem($id);
    return view('Admin.Gold.edit', compact('goldItem'));
}

public function update(UpdateGoldItemRequest $request, $id)
{
    $this->goldItemService->updateGoldItem($request, $id);
    return redirect()->route('admin.inventory')->with('success', 'Item updated successfully');
}

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
            default:
                return redirect()->back()->with('error', 'Invalid action');
        }

        return redirect()->back()->with('success', $message);
    }catch (\Exception $e) {
        Log::error('Bulk action failed', [
            'action' => $action,
            'items' => $selectedItems,
            'error' => $e->getMessage()
        ]);
        return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
    }
}
    public function dashboard(): View
    {
        $totalWeightSoldByYearAndShop = $this->shopWeightAnalysisService->getTotalWeightSoldByYearAndShop();
        $totalWeightInventory = $this->shopWeightAnalysisService->getTotalWeightInventory();

        $salesTrends = $this->shopWeightAnalysisService->getSalesTrends();
        $topSellingItems = $this->popularModelsService->getPopularModels();
        $inventoryTurnover = $this->shopWeightAnalysisService->getInventoryTurnover();

        return view('admin.new-dashboard', [
            'salesTrends' => $salesTrends,
            'topSellingItems' => $topSellingItems,
            'inventoryTurnover' => $inventoryTurnover,
            'totalWeightSoldByYearAndShop' => $totalWeightSoldByYearAndShop,
            'totalWeightInventory' => $totalWeightInventory
        ]);
    }
    public function deletedItems()
    {
        $deletedItems = DeletedItemHistory::with('deletedBy')
            ->latest('deleted_at')
            ->paginate(20);
            
        return view('admin.deleted-items-history', compact('deletedItems'));
    }
  
}
