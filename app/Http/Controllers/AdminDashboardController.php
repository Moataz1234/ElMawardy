<?php
namespace App\Http\Controllers;

use App\Services\ShopWeightAnalysisService;
use App\Services\PopularModelsService;
use Illuminate\View\View;

use Illuminate\Http\Request;
// use App\Http\Requests\GoldItemRequest;
use App\Services\Admin_GoldItemService;

class AdminDashboardController extends Controller
{
    protected $goldItemService;

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
    public function index(Request $request)
    {
        $goldItems = $this->goldItemService->getGoldItems($request);

        return view('Admin.Gold.Inventory_list', [
            'goldItems' => $goldItems,
        ]);
    }
    public function edit($id)
{
    $goldItem = $this->goldItemService->findGoldItem($id);
    return view('Admin.Gold.edit', compact('goldItem'));
}

public function update(Request $request, $id)
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

    switch ($action) {
        case 'delete':
            $this->goldItemService->bulkDelete($selectedItems);
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
}
