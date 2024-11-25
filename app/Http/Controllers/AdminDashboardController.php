<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

use Illuminate\Http\Request;
// use App\Http\Requests\GoldItemRequest;
use App\Services\Admin_GoldItemService;

class AdminDashboardController extends Controller
{
    protected $goldItemService;

    public function __construct(Admin_GoldItemService $goldItemService)
    {
        $this->goldItemService = $goldItemService;
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
        $shopWeightAnalysis = Cache::remember('shop_weight_analysis', 300, function () {
            return DB::table('gold_items')
                ->leftJoin('gold_items_sold', function($join) {
                    $join->on('gold_items.kind', '=', 'gold_items_sold.kind')
                        ->on('gold_items.shop_name', '=', 'gold_items_sold.shop_name');
                })
                ->select(
                    'gold_items.shop_name',
                    DB::raw('COALESCE(SUM(gold_items_sold.weight), 0) as total_weight_sold'),
                    DB::raw('SUM(gold_items.weight) as total_weight_inventory')
                )
                ->groupBy('gold_items.shop_name')
                ->get();
        });

        $popularModels = Cache::remember('popular_models', 300, function () {
            return DB::table('gold_items_sold')
                ->select(
                    'model',
                    DB::raw('SUM(quantity) as total_quantity'),
                    DB::raw('SUM(weight) as total_weight')
                )
                ->groupBy('model')
                ->orderByDesc('total_quantity')
                ->limit(10)
                ->get();
        });

        return view('admin.admin-dashboard', [
            'shopWeightAnalysis' => $shopWeightAnalysis,
            'popularModels' => $popularModels
        ]);
    }
}