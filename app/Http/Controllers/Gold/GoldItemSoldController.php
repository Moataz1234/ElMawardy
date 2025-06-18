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
    
        // Fetch sold items for the selected date, grouped by model
        $soldItems = GoldItemSold::whereDate('sold_date', $date)
            ->get()
            ->groupBy('model');
    
        // Calculate total items sold for the selected day
        $totalItemsSold = GoldItemSold::whereDate('sold_date', $date)->count();
    
        $reportsData = [];
    
        foreach ($soldItems as $model => $items) {
            // Check if this is a model with a character variant (A, B, C, D)
            $hasVariant = preg_match('/(.*)-([A-D])$/', $model, $matches);
            $baseModel = $hasVariant ? $matches[1] : $model;
            $variant = $hasVariant ? $matches[2] : null;
            
            // Get model info based on exact model match first, then fallback to base model
            $modelInfo = Models::where('model', $model)->first();
    
            // If no model info found and this is a variant, try the base model
            if (!$modelInfo && $hasVariant) {
                $modelInfo = Models::where('model', $baseModel)->first();
            }
        
            // Ensure we get correct source and stars
            $source = null;
            $stars = null;
            
            if ($modelInfo) {
                // Use model info from database if available
                $source = $modelInfo->source;
                $stars = $modelInfo->stars;
            } else {
                // Otherwise use sold item data
                $source = $items->first()->source;
                $stars = $items->first()->stars;
            }
        
            // Get workshop data from for_production table
            $workshopData = \App\Models\ForProduction::where('model', $model)
                ->orWhere('model', $baseModel)
                ->first();

            // Determine which variants exist for this base model
            $existingVariants = [];
            foreach (['A', 'B', 'C', 'D'] as $variantLetter) {
                $variantModel = $baseModel . '-' . $variantLetter;
                // Check if variant exists in Models table or has inventory/sold items
                if (Models::where('model', $variantModel)->exists() || 
                    GoldItem::where('model', $variantModel)->exists() || 
                    GoldItemSold::where('model', $variantModel)->exists()) {
                    $existingVariants[] = $variantLetter;
                }
            }
    
            // Step 1: Get all items for the model from GoldItems, GoldItemsSold, and AddRequests
            $inventoryItems = GoldItem::where('model', $model)->get();
            $soldItemsForModel = GoldItemSold::where('model', $model)->get();
            $addRequestItems = \App\Models\AddRequest::where('model', $model)->get();
                
            Log::info("Model: $model - Inventory Items: " . $inventoryItems->count());
            Log::info("Model: $model - Sold Items: " . $soldItemsForModel->count());
            Log::info("Model: $model - Add Request Items: " . $addRequestItems->count());
            
            // Step 2: Find the latest date from rest_since (GoldItems, AddRequests) and add_date (GoldItemsSold)
            // Only consider items where talab = 0
            $latestInventoryDate = $inventoryItems->where('talab', 0)->max('rest_since');
            $latestSoldDate = $soldItemsForModel->where('talab', 0)->max('add_date');
            $latestAddRequestDate = $addRequestItems->where('talab', 0)->max('rest_since');

            $latestDate = null;
            $dates = array_filter([$latestInventoryDate, $latestSoldDate, $latestAddRequestDate]);
            if (!empty($dates)) {
                $latestDate = max($dates);
            }
            Log::info("Model: $model - Latest Date: " . ($latestDate ? $latestDate : 'No Date Found'));
            
            // Step 3: Calculate the quantity for the latest date or for "old" items
            $lastProductionQuantity = 0;
            $oldItemsQuantity = 0;

            if ($latestDate) {
                // Calculate quantity for the latest date where talab = 0
                $lastProductionQuantity += $inventoryItems
                    ->where('rest_since', $latestDate)
                    ->where('talab', 0)
                    ->sum('quantity');

                $lastProductionQuantity += $soldItemsForModel
                    ->where('add_date', $latestDate)
                    ->where('talab', 0)
                    ->sum('quantity');

                $lastProductionQuantity += $addRequestItems
                    ->where('rest_since', $latestDate)
                    ->where('talab', 0)
                    ->sum('quantity');
                Log::info("Model: $model - Last Production Quantity: " . $lastProductionQuantity);
            } else {
                // Calculate quantity for items with null dates (old items) where talab = 0
                $oldItemsQuantity += $inventoryItems
                    ->whereNull('rest_since')
                    ->where('talab', 0)
                    ->sum('quantity');

                $oldItemsQuantity += $soldItemsForModel
                    ->whereNull('add_date')
                    ->where('talab', 0)
                    ->sum('quantity');

                $oldItemsQuantity += $addRequestItems
                    ->whereNull('rest_since')
                    ->where('talab', 0)
                    ->sum('quantity');
                Log::info("Model: $model - Old Items Quantity: " . $oldItemsQuantity);
            }
    
            // Step 4: Prepare the last production data
            $lastProductionDisplay = $latestDate
                ? Carbon::parse($latestDate)->format('d-m-Y') . ' (Qty: ' . $lastProductionQuantity . ')'
                : 'Old (Qty: ' . $oldItemsQuantity . ')';
    
            // Define our variant color mappings
            $variantColors = [
                'A' => 'Black',
                'B' => 'Yellow',
                'C' => 'Red',
                'D' => 'Blue'
            ];
            
    
            // Get source and stars from model info if available, otherwise fallback to item data
            $source = $modelInfo ? $modelInfo->source : $items->first()->source;
            $stars = $modelInfo ? $modelInfo->stars : $items->first()->stars;
    
            $reportsData[$model] = [
                'workshop_count' => $items->where('shop_name', 'Workshop')->count(),
                'order_date' => $date,
                'gold_color' => $items->first()->gold_color,
                'source' => $source,
                'stars' => $stars,
                'image_path' => $modelInfo ? $modelInfo->scanned_image : null,
                'model' => $model,
                'base_model' => $baseModel,
                'variant' => $variant,
                'variant_color' => $variant ? $variantColors[$variant] : null,
                'existing_variants' => $existingVariants,
                'remaining' => GoldItem::where('model', $model)->count(),
                'total_production' => GoldItem::where('model', $model)->count() + GoldItemSold::where('model', $model)->count(),
                'total_sold' => GoldItemSold::where('model', $model)->count(),
                'first_production' => $modelInfo && $modelInfo->first_production 
                    ? $modelInfo->first_production 
                    : 'Old',
                'shop' => $items->pluck('shop_name')->unique()->implode(' / '),
                'pieces_sold_today' => $items->count(),
                'shops_data' => $this->getShopDistribution($model, $baseModel),
                'last_production' => $lastProductionDisplay,
                'workshop_data' => $workshopData ? [
                    'not_finished' => $workshopData->not_finished,
                    'order_date' => $workshopData->order_date->format('d-m-Y')
                ] : [
                    'not_finished' => 0,
                    'order_date' => 'No Order'
                ],
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
                'recipients' => $recipients, 
                'isPdf' => true 
            ]);
            $pdf->setPaper('A4', 'portrait');
            return $pdf->stream('sales_report_' . $date . '.pdf');
        }
    
        // Return the view for HTML display
        return view('Admin.Reports.view', [
            'reportsData' => $reportsData,
            'selectedDate' => $date,
            'totalItemsSold' => $totalItemsSold,
            'recipients' => $recipients,
            'isPdf' => false
        ]);
    }
    /**
     * Helper method to get shop distribution for a specific model.
     */
    private function getShopDistribution($model, $baseModel = null)
    {
        $shops = [
            'Mohandessin Shop', 'Mall of Arabia', 'Nasr City', 'Zamalek',
            'Mall of Egypt', 'EL Guezira Shop', 'Arkan', 'District 5', 'U Venues'
        ];
        
        // Use base model if provided, otherwise extract from model
        if (!$baseModel) {
            $baseModel = preg_replace('/-[A-D]$/', '', $model);
        }
        
        $shopDistribution = [];
        
        foreach ($shops as $shop) {
            // Get the base model items ONLY (not variants)
            $baseItems = GoldItem::where('model', $baseModel)->where('shop_name', $shop)->get();
            
            // Get variant items for all variants (for counting only)
            $variantAItems = GoldItem::where('model', $baseModel . '-A')->where('shop_name', $shop)->get();
            $variantBItems = GoldItem::where('model', $baseModel . '-B')->where('shop_name', $shop)->get();
            $variantCItems = GoldItem::where('model', $baseModel . '-C')->where('shop_name', $shop)->get();
            $variantDItems = GoldItem::where('model', $baseModel . '-D')->where('shop_name', $shop)->get();
            
            // Calculate gold color counts ONLY from base model
            $whiteGold = $baseItems->where('gold_color', 'White')->count();
            $yellowGold = $baseItems->where('gold_color', 'Yellow')->count();
            $roseGold = $baseItems->where('gold_color', 'Rose')->count();
            
            // Get variant counts by character (for All Rests column only)
            $variantA = $variantAItems->count();
            $variantB = $variantBItems->count();
            $variantC = $variantCItems->count();
            $variantD = $variantDItems->count();
            
            // Initialize shop data
            $shopData = [
                'white_gold' => $whiteGold,
                'yellow_gold' => $yellowGold,
                'rose_gold' => $roseGold,
                'variant_white' => 0, // Not used anymore since we only show base model colors
                'variant_yellow' => 0, // Not used anymore since we only show base model colors
                'variant_rose' => 0, // Not used anymore since we only show base model colors
                'variant_A' => $variantA,
                'variant_B' => $variantB,
                'variant_C' => $variantC,
                'variant_D' => $variantD,
            ];
            
            // For all_rests: base model total (sum of all gold colors) + all variants
            $baseModelTotal = $whiteGold + $yellowGold + $roseGold;
            $shopData['all_rests'] = $baseModelTotal + $variantA + $variantB + $variantC + $variantD;
            
            // Store in distribution data
            $shopDistribution[$shop] = $shopData;
        }
        
        return $shopDistribution;
    }
}
