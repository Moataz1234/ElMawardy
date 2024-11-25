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
<<<<<<< HEAD
        // Get all unique kinds from inventory
        $allKinds = GoldItem::select('kind')
            ->distinct()
            ->get()
            ->pluck('kind');
    
        // Previous dashboard statistics
        $todayStats = GoldItemSold::whereDate('sold_date', today())
            ->select(
                'shop_name',
                DB::raw('COUNT(*) as total_items'),
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('SUM(weight) as total_weight')
            )
            ->groupBy('shop_name')
            ->get();
    
        $popularModels = GoldItemSold::select(
                'model',
                DB::raw('COUNT(*) as sold_count'),
                DB::raw('SUM(quantity) as total_quantity')
            )
            ->groupBy('model')
            ->orderByDesc('sold_count')
            ->limit(10)
            ->get();
    
        // Modified weight analysis to include all kinds
        $weightAnalysis = GoldItemSold::rightJoin('gold_items', 'gold_items.kind', '=', 'gold_items_sold.kind')
            ->select(
                'gold_items.kind',
                DB::raw('COALESCE(SUM(gold_items_sold.weight), 0) as total_weight_sold'),
                DB::raw('COUNT(gold_items_sold.id) as items_count')
            )
            ->groupBy('gold_items.kind')
            ->get();
    
        // Modified inventory analysis to include all kinds
        $inventoryAnalysis = GoldItem::select(
                'kind',
                DB::raw('SUM(weight) as total_weight_inventory'),
                DB::raw('COUNT(*) as items_in_stock')
            )
            ->groupBy('kind')
            ->get();
    
        // Add sales by category for all kinds
        $salesByCategory = GoldItem::leftJoin('gold_items_sold', 'gold_items.kind', '=', 'gold_items_sold.kind')
            ->select(
                'gold_items.kind',
                DB::raw('COALESCE(SUM(gold_items_sold.quantity), 0) as total_quantity')
            )
            ->groupBy('gold_items.kind')
            ->get();
    
        $todaySales = GoldItemSold::whereDate('sold_date', today())->sum('price');
        $yesterdaySales = GoldItemSold::whereDate('sold_date', today()->subDay())->sum('price');
        $percentChange = $yesterdaySales > 0 ? (($todaySales - $yesterdaySales) / $yesterdaySales) * 100 : 0;
    
            $shopWeightAnalysis = GoldItemSold::rightJoin('gold_items', function($join) {
                $join->on('gold_items.kind', '=', 'gold_items_sold.kind')
                    ->on('gold_items.shop_name', '=', 'gold_items_sold.shop_name');
            })
            ->select(
                'gold_items.shop_name',
                'gold_items.kind',
                DB::raw('COALESCE(SUM(gold_items_sold.weight), 0) as total_weight_sold'),
                DB::raw('SUM(gold_items.weight) as total_weight_inventory'),
                DB::raw('COUNT(gold_items_sold.id) as items_sold'),
                DB::raw('COUNT(gold_items.id) as items_in_stock')
            )
            ->groupBy('gold_items.shop_name', 'gold_items.kind')
            ->get();
        
            // Most sold category by shop today
            $topCategoryByShopToday = GoldItemSold::whereDate('sold_date', today())
                ->select(
                    'shop_name',
                    'kind',
                    DB::raw('SUM(quantity) as total_quantity')
                )
                ->groupBy('shop_name', 'kind')
                ->orderByRaw('SUM(quantity) DESC')
                ->get()
                ->groupBy('shop_name')
                ->map(function($items) {
                    return $items->first();
                });
        
            // Most sold category by shop overall
            $topCategoryByShopOverall = GoldItemSold::select(
                    'shop_name',
                    'kind',
                    DB::raw('SUM(quantity) as total_quantity')
                )
                ->groupBy('shop_name', 'kind')
                ->orderByRaw('SUM(quantity) DESC')
                ->get()
                ->groupBy('shop_name')
                ->map(function($items) {
                    return $items->first();
                });
        
            // Add these to your existing return statement
            return view('admin.admin-dashboard', compact(
                'todayStats',
                'popularModels',
                'weightAnalysis',
                'inventoryAnalysis',
                'todaySales',
                'yesterdaySales',
                'percentChange',
                'salesByCategory',
                'shopWeightAnalysis',
                'topCategoryByShopToday',
                'topCategoryByShopOverall'
            ));
=======
        $this->goldItemService = $goldItemService;
>>>>>>> f6b866230d849c5df5c291e82aeecf4c795c326e
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
<<<<<<< HEAD
    // Get weights sold grouped by kind
    $weightsSold = GoldItemSold::select(
        'kind',
        DB::raw('SUM(weight) as total_weight_sold'),
        DB::raw('COUNT(*) as items_count')
    )
    ->groupBy('kind')
    ->get();

    // Get inventory weights grouped by kind
    $weightsInventory = GoldItem::select(
        'kind',
        DB::raw('SUM(weight) as total_weight_inventory'),
        DB::raw('COUNT(*) as items_in_stock')
    )
    ->groupBy('kind')
    ->get();

    // Get today's sales analysis
    $todaySales = GoldItemSold::whereDate('sold_date', today())
        ->select(DB::raw('SUM(price) as total_price'))
        ->first();

    // Get previous day's sales for comparison
    $previousSales = GoldItemSold::whereDate('sold_date', today()->subDay())
        ->select(DB::raw('SUM(price) as total_price'))
        ->first();

    return view('admin.weight-analysis', compact(
        'weightsSold',
        'weightsInventory',
        'todaySales',
        'previousSales'
    ));
=======
    $goldItem = $this->goldItemService->findGoldItem($id);
    return view('Admin.Gold.edit', compact('goldItem'));
>>>>>>> f6b866230d849c5df5c291e82aeecf4c795c326e
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