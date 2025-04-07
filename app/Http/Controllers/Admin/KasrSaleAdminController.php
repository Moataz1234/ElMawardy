<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KasrSale;
use App\Models\KasrItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
        
        return view('admin.kasr_sales.index', compact(
            'kasrSales', 
            'shops', 
            'totalOriginalWeight', 
            'totalNetWeight',
            'pendingCount'
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
        
        $kasrSale->status = $validated['status'];
        $kasrSale->save();
        
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
        
        // Update all selected orders
        KasrSale::whereIn('id', $validated['selected_orders'])
            ->update(['status' => $status]);
        
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
} 