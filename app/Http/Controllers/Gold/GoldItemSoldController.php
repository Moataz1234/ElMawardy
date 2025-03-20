<?php

namespace App\Http\Controllers\Gold;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\GoldItemSold;
use App\Models\Models;
use App\Models\Customer;
use App\Models\GoldItem;
use App\Services\GoldItemSoldService;
use App\Services\GoldPoundSoldService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;



class GoldItemSoldController extends Controller
{
    protected $goldItemSoldService;
    protected $goldPoundSoldService;

    public function __construct(
        GoldItemSoldService $goldItemSoldService,
        GoldPoundSoldService $goldPoundSoldService
    ) {
        $this->goldItemSoldService = $goldItemSoldService;
        $this->goldPoundSoldService = $goldPoundSoldService;
    }

    public function index(Request $request)
    {
        $goldItems = $this->goldItemSoldService->getGoldItemsSold($request);
        $goldPounds = $this->goldPoundSoldService->getGoldPoundsSold($request);

        // Get unique values for filters
        $gold_color = GoldItemSold::distinct()->pluck('gold_color')->filter();
        $kind = GoldItemSold::distinct()->pluck('kind')->filter();

        return view('Shops.Gold.sold_index', [
            'goldItems' => $goldItems,
            'goldPounds' => $goldPounds,
            'search' => $request->input('search'),
            'sort' => $request->input('sort', 'serial_number'),
            'direction' => $request->input('direction', 'asc'),
            'gold_color' => $gold_color,
            'kind' => $kind
        ]);
    }
    public function edit(string $id)
    {
        $goldItemSold = GoldItemSold::findOrFail($id);
        return view('admin.Gold.Edit_sold_form', compact('goldItemSold'));
    }

    /**
     * Update the specified sold item in storage.
     */
    public function update(Request $request, string $id)
    {
        $goldItemSold = GoldItemSold::findOrFail($id);

        $validated = $request->validate([
            'link' => 'nullable|string',
            'serial_number' => 'nullable|string',
            'shop_name' => 'nullable|string',
            'shop_id' => 'nullable|integer',
            'kind' => 'nullable|string',
            'model' => 'nullable|string',
            'talab' => 'nullable|string',
            'gold_color' => 'nullable|string',
            'stones' => 'nullable|string',
            'metal_type' => 'nullable|string',
            'metal_purity' => 'nullable|string',
            'quantity' => 'nullable|integer',
            'weight' => 'nullable|numeric',
            'add_date' => 'nullable|date',
            'source' => 'nullable|string',
            'to_print' => 'nullable|boolean',
            'price' => 'nullable|numeric',
            'semi_or_no' => 'nullable|string',
            'average_of_stones' => 'nullable|numeric',
            'net_weight' => 'nullable|numeric',
            'sold_date' => 'nullable|date',
        ]);

        if ($request->hasFile('link')) {
            $image = $request->file('link');
            $imagePath = $image->store('uploads/gold_items_sold', 'public');
            $validated['link'] = $imagePath;
        }

        $goldItemSold->update($validated);

        return redirect()->route('admin.sold-items')->with('success', 'Sold gold item updated successfully.');
    }

    /**
     * Mark the specified item as sold and transfer to the sold table.
     */


    public function markAsRest(Request $request, string $id)
    {
        $goldItem = GoldItemSold::findOrFail($id);

        // Transfer data to GoldItemSold
        GoldItem::create($goldItem->toArray());

        // Delete the item from GoldItem
        $goldItem->delete();

        return redirect()->route('gold-items.sold')->with('success', 'Gold item marked as rest successfully.');
    }
    
    public function viewReports(Request $request)
    {
        // Get the selected date from the request or default to today
        $date = $request->input('date', now()->format('Y-m-d'));
    
        // Fetch sold items for the selected date, grouped by model, and join with the Models table
        $soldItems = GoldItemSold::whereDate('sold_date', $date)
        ->get()
        ->groupBy('model');
    
        // Calculate total items sold for the selected day
        $totalItemsSold = GoldItemSold::whereDate('sold_date', $date)->count();
    
        $reportsData = [];
    
      
    foreach ($soldItems as $model => $items) {
        $modelInfo = Models::where('model', $model)->first();

        // Step 1: Get all items for the model from GoldItems and GoldItemsSold
        $inventoryItems = GoldItem::where('model', $model)
            // ->where('talab', false)
            // ->whereHas('modelCategory', function ($query) {
            //     $query->where('source', 'Production');
            // })
            ->get();

        $soldItemsForModel = GoldItemSold::where('model', $model)
            // ->where('talab', false)
            // ->whereHas('modelCategory', function ($query) {
            //     $query->where('source', 'Production');
            // })
            ->get();
            
        Log::info("Model: $model - Inventory Items: " . $inventoryItems->count());
        Log::info("Model: $model - Sold Items: " . $soldItemsForModel->count());
        // Step 2: Find the latest date from rest_since and add_date
        $latestInventoryDate = $inventoryItems->max('rest_since');
        $latestSoldDate = $soldItemsForModel->max('add_date');

        $latestDate = null;
        if ($latestInventoryDate && $latestSoldDate) {
            $latestDate = $latestInventoryDate > $latestSoldDate ? $latestInventoryDate : $latestSoldDate;
        } elseif ($latestInventoryDate) {
            $latestDate = $latestInventoryDate;
        } elseif ($latestSoldDate) {
            $latestDate = $latestSoldDate;
        }
        Log::info("Model: $model - Latest Date: " . ($latestDate ? $latestDate : 'No Date Found'));
        // Step 3: Calculate the quantity for the latest date or for "old" items
        $lastProductionQuantity = 0;
        $oldItemsQuantity = 0;

        if ($latestDate) {
            // Calculate quantity for the latest date
            $lastProductionQuantity += $inventoryItems
                ->where('rest_since', $latestDate)
                ->sum('quantity');

            $lastProductionQuantity += $soldItemsForModel
                ->where('add_date', $latestDate)
                ->sum('quantity');
                Log::info("Model: $model - Last Production Quantity: " . $lastProductionQuantity);

        }

         else {
            // Calculate quantity for items with null dates (old items)
            $oldItemsQuantity += $inventoryItems
                ->whereNull('rest_since')
                ->sum('quantity');

            $oldItemsQuantity += $soldItemsForModel
                ->whereNull('add_date')
                ->sum('quantity');
                Log::info("Model: $model - Old Items Quantity: " . $oldItemsQuantity);

        }

        // Step 4: Prepare the last production data
        $lastProductionDisplay = $latestDate
            ? Carbon::parse($latestDate)->format('d-m-Y') . ' (Qty: ' . $lastProductionQuantity . ')'
            : 'Old (Qty: ' . $oldItemsQuantity . ')';

    
            $reportsData[$model] = [
                'workshop_count' => $items->where('shop_name', 'Workshop')->count(),
                'order_date' => $date,
                'gold_color' => $items->first()->gold_color,
                'source' => $items->first()->source, // Get source directly from GoldItemSold
                'stars' => $modelInfo ? $modelInfo->stars : $items->first()->stars,
                'image_path' => $modelInfo ? $modelInfo->scanned_image : null,
                'model' => $model,
                'remaining' => GoldItem::where('model', $model)->count(),
                'total_production' => GoldItem::where('model', $model)->count() + GoldItemSold::where('model', $model)->count(),
                'total_sold' => GoldItemSold::where('model', $model)->count(),
                'first_production' => $modelInfo && $modelInfo->first_production 
                    ? $modelInfo->first_production 
                    : 'Old',
                'shop' => $items->pluck('shop_name')->unique()->implode(' / '),
                'pieces_sold_today' => $items->count(),
                'shops_data' => $this->getShopDistribution($model),
                'last_production' => $lastProductionDisplay,
            ];
        }
    
        // Get static recipients from config
        $recipients = array_merge(
            config('reports_email.elmawardy_recipients', []),
            config('reports_email.gmail_recipients', [])
        );
        
        // Return the view for HTML display
        if ($request->has('export') && $request->input('export') === 'pdf') {
            $pdf = PDF::loadView('Admin.Reports.view', [
                'reportsData' => $reportsData,
                'selectedDate' => $date,
                'totalItemsSold' => $totalItemsSold,
                'recipients' => $recipients, // Pass recipients to the view
                'isPdf' => true // Add a flag to indicate PDF export
            ]);
            $pdf->setPaper('A4', 'landscape');
            return $pdf->stream('sales_report_' . $date . '.pdf');
        }
    
        // Return the view for HTML display
        return view('Admin.Reports.view', [
            'reportsData' => $reportsData,
            'selectedDate' => $date,
            'totalItemsSold' => $totalItemsSold,
            'recipients' => $recipients, // Pass recipients to the view
            'isPdf' => false // Add a flag to indicate HTML display
        ]);
    }
    /**
     * Helper method to get shop distribution for a specific model.
     */
    private function getShopDistribution($model)
    {
        $shops = [
            'Mohandessin Shop', 'Mall of Arabia', 'Nasr City', 'Zamalek',
            'Mall of Egypt', 'EL Guezira Shop', 'Arkan', 'District 5', 'U Venues'
        ];
    
        $shopDistribution = [];
    
        foreach ($shops as $shop) {
            $shopItems = GoldItem::where('model', $model)->where('shop_name', $shop)->get();
    
            $shopDistribution[$shop] = [
                'all_rests' => $shopItems->count(),
                'white_gold' => $shopItems->where('gold_color', 'White')->count(),
                'yellow_gold' => $shopItems->where('gold_color', 'Yellow')->count(),
                'rose_gold' => $shopItems->where('gold_color', 'Rose')->count()
            ];
        }
    
        return $shopDistribution;
    }
}
