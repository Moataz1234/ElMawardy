<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KasrSale;
use App\Models\KasrItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class KasrSaleAdminController extends Controller
{
    public function index(Request $request)
    {
        // Start with a base query with eager loading of items
        $query = KasrSale::with('items');
        
        // Apply date range filter
        if ($request->filled('date_range')) {
            $dates = explode(' - ', $request->date_range);
            if (count($dates) == 2) {
                $query->whereBetween('order_date', [
                    Carbon::parse($dates[0])->startOfDay(),
                    Carbon::parse($dates[1])->endOfDay(),
                ]);
            }
        }
        
        // Apply shop name filter
        if ($request->filled('shop_name')) {
            $query->where('shop_name', $request->shop_name);
        }
        
        // Apply price range filter
        if ($request->filled('price_min')) {
            $query->where('offered_price', '>=', $request->price_min);
        }
        
        if ($request->filled('price_max')) {
            $query->where('offered_price', '<=', $request->price_max);
        }
        
        // Apply status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            // If no status filter is provided, default to showing only pending
            $query->where('status', 'pending');
        }
        
        // Get all records for calculations (without pagination)
        $allRecords = $query->get();
        
        // Calculate total weights
        $totalOriginalWeight = 0;
        $totalNetWeight = 0;
        
        foreach ($allRecords as $sale) {
            $totalOriginalWeight += $sale->getTotalWeight();
            $totalNetWeight += $sale->getTotalNetWeight();
        }
        
        // Count pending orders
        $pendingCount = KasrSale::where('status', 'pending')->count();
        
        // Get the filtered results with pagination
        $kasrSales = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Get all shop names for the filter dropdown
        $shops = User::whereNotNull('shop_name')->select('shop_name')->distinct()->get();

        // Prepare structured data for items table
        $acceptedSales = KasrSale::where('status', 'accepted')
            ->with('items')
            ->get();

        $structuredData = [];
        $purities = [];
        $shopNames = [];

        foreach ($acceptedSales as $sale) {
            $shopName = $sale->shop_name;
            $shopNames[] = $shopName;

            foreach ($sale->items as $item) {
                $purity = $item->metal_purity;
                $purities[] = $purity;

                if (!isset($structuredData[$shopName])) {
                    $structuredData[$shopName] = [];
                }

                if (!isset($structuredData[$shopName][$purity])) {
                    $structuredData[$shopName][$purity] = 0;
                }

                $structuredData[$shopName][$purity] += $item->net_weight;
            }
        }

        // Remove duplicates and sort
        $purities = array_unique($purities);
        sort($purities);
        $shopNames = array_unique($shopNames);
        sort($shopNames);
        
        return view('admin.kasr_sales.index', compact(
            'kasrSales', 
            'shops', 
            'totalOriginalWeight', 
            'totalNetWeight',
            'pendingCount',
            'structuredData',
            'purities',
            'shopNames'
        ));
    }
    
    /**
     * Update the status of a specific kasr sale.
     */
    public function updateStatus(Request $request, KasrSale $kasrSale)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,accepted,rejected',
        ]);
        
        // Set the status on the original kasr sale
        $kasrSale->status = $validated['status'];
        $kasrSale->save();
        
        // If accepting, create a completed sale record
        if ($validated['status'] == 'accepted') {
            \App\Models\KasrSaleComplete::create([
                'original_kasr_sale_id' => $kasrSale->id,
                'customer_name' => $kasrSale->customer_name,
                'customer_phone' => $kasrSale->customer_phone,
                'original_shop_name' => $kasrSale->shop_name, // Store the original shop name
                'shop_name' => 'rabea', // Set shop_name to 'rabea' for completed sales
                'image_path' => $kasrSale->image_path,
                'offered_price' => $kasrSale->offered_price,
                'order_date' => $kasrSale->order_date,
                'completion_date' => now(),
                'status' => 'accepted',
            ]);
        }
        
        $statusText = $validated['status'] == 'accepted' ? 'قبول' : 'رفض';
        
        return redirect()->back()->with('success', "تم {$statusText} الطلب بنجاح");
    }

    /**
     * Process batch actions for kasr sales.
     */
    public function batchUpdate(Request $request)
    {
        $validated = $request->validate([
            'selected_orders' => 'required|array',
            'selected_orders.*' => 'exists:kasr_sales,id',
            'action' => 'required|in:accept,reject',
        ]);
        
        $status = $request->action == 'accept' ? 'accepted' : 'rejected';
        $count = count($validated['selected_orders']);
        
        if ($request->action == 'accept') {
            // Get all the kasr sales that are being accepted
            $kasrSales = KasrSale::whereIn('id', $validated['selected_orders'])->get();
            
            // For each accepted sale, create a completed sale record
            foreach ($kasrSales as $kasrSale) {
                // Create a new completed sale record
                \App\Models\KasrSaleComplete::create([
                    'original_kasr_sale_id' => $kasrSale->id,
                    'customer_name' => $kasrSale->customer_name,
                    'customer_phone' => $kasrSale->customer_phone,
                    'original_shop_name' => $kasrSale->shop_name, // Store the original shop name
                    'shop_name' => 'rabea', // Set shop_name to 'rabea' for completed sales
                    'image_path' => $kasrSale->image_path,
                    'offered_price' => $kasrSale->offered_price,
                    'order_date' => $kasrSale->order_date,
                    'completion_date' => now(),
                    'status' => 'accepted',
                ]);
            }
            
            // Update the original kasr sales to accepted status (but keep their original shop_name)
            KasrSale::whereIn('id', $validated['selected_orders'])
                ->update([
                    'status' => 'accepted',
                    // Don't change the shop_name in the original records
                ]);
        } else {
            // For reject action, just update the status
            KasrSale::whereIn('id', $validated['selected_orders'])
                ->update([
                    'status' => 'rejected',
                ]);
        }
        
        $actionText = $request->action == 'accept' ? 'قبول' : 'رفض';
        
        return redirect()->back()->with('success', "تم {$actionText} {$count} طلب بنجاح");
    }
    
    /**
     * Show details for a specific sale.
     */
    public function show(KasrSale $kasrSale)
    {
        $kasrSale->load('items');
        return view('admin.kasr_sales.show', compact('kasrSale'));
    }

    public function getItems(KasrSale $kasrSale)
    {
        try {
            $kasrSale->load('items');
            $items = $kasrSale->items->map(function($item) {
                return [
                    'kind' => $item->kind,
                    'metal_purity' => $item->metal_purity,
                    'weight' => number_format($item->weight, 2),
                    'net_weight' => number_format($item->net_weight, 2),
                    'item_type' => $item->item_type,
                ];
            });
            
            return response()->json([
                'items' => $items->toArray() // Make sure to convert to array
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getItems method', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
} 