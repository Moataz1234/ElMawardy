<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KasrSale;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KasrSaleAdminController extends Controller
{
    public function index(Request $request)
    {
        // Start with a base query
        $query = KasrSale::query();
        
        // Apply date range filter
        if ($request->filled('date_range')) {
            $dates = explode(' - ', $request->date_range);
            if (count($dates) == 2) {
                $query->whereBetween('order_date', [$dates[0], $dates[1]]);
            }
        }
        
        // Apply shop name filter
        if ($request->filled('shop_name')) {
            $query->where('shop_name', $request->shop_name);
        }
        
        // Apply kind filter
        if ($request->filled('kind')) {
            $query->where('kind', $request->kind);
        }
        
        // Apply metal purity filter
        if ($request->filled('metal_purity')) {
            $query->where('metal_purity', $request->metal_purity);
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
        }
        
        // Get all records for weight calculations (without pagination)
        $allRecords = $query->get();
        
        // Calculate total weights
        $totalOriginalWeight = 0;
        $total24kWeight = 0;
        $total18kWeight = 0;
        
        foreach ($allRecords as $record) {
            // Extract the numeric value from the purity string (e.g., "21K" -> 21)
            $purityValue = intval(str_replace('K', '', $record->metal_purity));
            
            // Add to totals
            $totalOriginalWeight += $record->weight;
            $total24kWeight += ($purityValue / 24) * $record->weight;
            $total18kWeight += ($purityValue / 18) * $record->weight;
        }
        
        // Get the filtered results with pagination
        $kasrSales = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Get all shop names for the filter dropdown
        $shops = User::whereNotNull('shop_name')->select('shop_name')->distinct()->get();
        
        // Get all unique kinds for the filter dropdown
        $kinds = KasrSale::whereNotNull('kind')->select('kind')->distinct()->pluck('kind');
        
        return view('admin.kasr_sales.index', compact(
            'kasrSales', 
            'shops', 
            'kinds', 
            'totalOriginalWeight', 
            'total24kWeight', 
            'total18kWeight'
        ));
    }
} 