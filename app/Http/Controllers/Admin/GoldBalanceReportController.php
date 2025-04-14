<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GoldItemSold;
use App\Models\KasrItem;
use App\Models\KasrSale;
use App\Models\KasrSaleComplete;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GoldBalanceReportController extends Controller
{
    public function index(Request $request)
    {
        // Get filter parameters - default to today if not provided
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::today();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : Carbon::today()->endOfDay();
        $hideInactive = $request->has('hide_inactive');
        
        // Query for KasrItems (bought gold)
        $kasrItemsQuery = KasrItem::query()
            ->join('kasr_sales', 'kasr_items.kasr_sale_id', '=', 'kasr_sales.id')
            ->where('kasr_sales.status', 'accepted');
            
        // Apply date filters to KasrItems
        $kasrItemsQuery->whereBetween('kasr_sales.order_date', [$startDate, $endDate]);
        
        // Get kasr items with normalized weight to 18K
        $kasrItems = $kasrItemsQuery->get();
        
        // Query for KasrItems from completed sales table
        $completedKasrItemsQuery = KasrItem::query()
            ->join('kasr_sales', 'kasr_items.kasr_sale_id', '=', 'kasr_sales.id')
            ->join('kasr_sales_complete', 'kasr_sales.id', '=', 'kasr_sales_complete.original_kasr_sale_id')
            ->where('kasr_sales_complete.status', 'accepted');
            
        // Apply date filters to completed items
        $completedKasrItemsQuery->whereBetween('kasr_sales_complete.order_date', [$startDate, $endDate]);
        
        // Get completed kasr items
        $completedKasrItems = $completedKasrItemsQuery->get();
        
        // Combine both collections
        $allKasrItems = $kasrItems->concat($completedKasrItems);
        
        // Calculate total bought weight
        $totalBoughtWeight = $this->normalizeWeightsTo18K($allKasrItems);
        
        // Query for Gold Items Sold
        $soldItemsQuery = GoldItemSold::query();
        
        // Apply date filters to sold items
        $soldItemsQuery->whereBetween('sold_date', [$startDate, $endDate]);
        
        // Get sold items with normalized weight to 18K
        $soldItems = $soldItemsQuery->get();
        $totalSoldWeight = $this->normalizeWeightsTo18K($soldItems, 'sold');
        
        // Calculate balance
        $balance = $totalSoldWeight - $totalBoughtWeight;
        
        // Get weight by purity
        $boughtWeightByPurity = $this->getWeightByPurity($allKasrItems);
        $soldWeightByPurity = $this->getWeightByPurity($soldItems, 'sold');
        
        // Get monthly data for chart
        $monthlyData = $this->getMonthlyData($startDate->copy()->subMonths(6), $endDate);
        
        // Get shop-based report data
        $shopReportData = $this->getShopReportData($startDate, $endDate, $hideInactive);
        
        return view('admin.reports.gold-balance', compact(
            'totalBoughtWeight', 
            'totalSoldWeight', 
            'balance', 
            'boughtWeightByPurity', 
            'soldWeightByPurity',
            'monthlyData',
            'startDate',
            'endDate',
            'shopReportData',
            'hideInactive'
        ));
    }
    
    /**
     * Normalize weights to 18K purity
     * 
     * @param \Illuminate\Support\Collection $items
     * @param string $type 'kasr' or 'sold'
     * @return float
     */
    private function normalizeWeightsTo18K($items, $type = 'kasr')
    {
        $totalNormalizedWeight = 0;
        
        foreach ($items as $item) {
            $weight = $type === 'kasr' ? $item->weight : $item->weight;
            $purity = $type === 'kasr' ? $item->metal_purity : $item->metal_purity;
            
            // Skip items with no purity
            if (!$purity) {
                continue;
            }
            
            // Extract numeric value from purity (e.g., "21K" -> 21)
            $purityValue = (int) preg_replace('/[^0-9]/', '', $purity);
            
            // Normalize to 18K
            // Formula: weight * (current purity / target purity)
            $normalizedWeight = $weight * ($purityValue / 18);
            $totalNormalizedWeight += $normalizedWeight;
        }
        
        return $totalNormalizedWeight;
    }
    
    /**
     * Get weight by purity
     * 
     * @param \Illuminate\Support\Collection $items
     * @param string $type 'kasr' or 'sold'
     * @return array
     */
    private function getWeightByPurity($items, $type = 'kasr')
    {
        $weightByPurity = [];
        
        foreach ($items as $item) {
            $weight = $type === 'kasr' ? $item->weight : $item->weight;
            $purity = $type === 'kasr' ? $item->metal_purity : $item->metal_purity;
            
            // Skip items with no purity
            if (!$purity) {
                continue;
            }
            
            if (!isset($weightByPurity[$purity])) {
                $weightByPurity[$purity] = 0;
            }
            
            $weightByPurity[$purity] += $weight;
        }
        
        return $weightByPurity;
    }
    
    /**
     * Get monthly data for charts
     * 
     * @param Carbon|null $startDate
     * @param Carbon|null $endDate
     * @return array
     */
    private function getMonthlyData($startDate = null, $endDate = null)
    {
        // Default to last 12 months if no dates provided
        if (!$startDate) {
            $startDate = Carbon::now()->subYear();
        }
        if (!$endDate) {
            $endDate = Carbon::now();
        }
        
        // Bought monthly data (KasrItems)
        $boughtMonthly = KasrItem::join('kasr_sales', 'kasr_items.kasr_sale_id', '=', 'kasr_sales.id')
            ->where('kasr_sales.status', 'accepted')
            ->whereBetween('kasr_sales.order_date', [$startDate, $endDate])
            ->select(
                DB::raw('YEAR(kasr_sales.order_date) as year'),
                DB::raw('MONTH(kasr_sales.order_date) as month'),
                DB::raw('SUM(kasr_items.weight) as total_weight'),
                'kasr_items.metal_purity'
            )
            ->groupBy('year', 'month', 'kasr_items.metal_purity')
            ->get();
            
        // Sold monthly data (GoldItemSold)
        $soldMonthly = GoldItemSold::whereBetween('sold_date', [$startDate, $endDate])
            ->select(
                DB::raw('YEAR(sold_date) as year'),
                DB::raw('MONTH(sold_date) as month'),
                DB::raw('SUM(weight) as total_weight'),
                'metal_purity'
            )
            ->groupBy('year', 'month', 'metal_purity')
            ->get();
        
        // Process and format data for chart
        $monthlyChartData = [];
        
        // Generate all months in the date range
        $currentDate = clone $startDate;
        while ($currentDate <= $endDate) {
            $yearMonth = $currentDate->format('Y-m');
            $monthlyChartData[$yearMonth] = [
                'bought' => 0,
                'sold' => 0,
                'balance' => 0,
                'date' => $currentDate->format('M Y')
            ];
            $currentDate->addMonth();
        }
        
        // Process bought data
        foreach ($boughtMonthly as $record) {
            $yearMonth = sprintf('%04d-%02d', $record->year, $record->month);
            if (isset($monthlyChartData[$yearMonth])) {
                // Extract numeric value from purity
                $purityValue = (int) preg_replace('/[^0-9]/', '', $record->metal_purity);
                // Normalize to 18K
                $normalizedWeight = $record->total_weight * ($purityValue / 18);
                $monthlyChartData[$yearMonth]['bought'] += $normalizedWeight;
            }
        }
        
        // Process sold data
        foreach ($soldMonthly as $record) {
            $yearMonth = sprintf('%04d-%02d', $record->year, $record->month);
            if (isset($monthlyChartData[$yearMonth])) {
                // Extract numeric value from purity
                $purityValue = (int) preg_replace('/[^0-9]/', '', $record->metal_purity);
                // Normalize to 18K
                $normalizedWeight = $record->total_weight * ($purityValue / 18);
                $monthlyChartData[$yearMonth]['sold'] += $normalizedWeight;
            }
        }
        
        // Calculate balance
        foreach ($monthlyChartData as &$data) {
            $data['balance'] = $data['sold'] - $data['bought'];
        }
        
        return array_values($monthlyChartData);
    }
    
    /**
     * Get shop-based report data
     * 
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @param bool $hideInactive
     * @return array
     */
    private function getShopReportData($startDate, $endDate, $hideInactive = false)
    {
        // Get all shop names from users table - only 'user' type, exclude 'admin' and 'rabea'
        $allShops = \App\Models\User::where('usertype', 'user')
            ->whereNotIn('usertype', ['admin', 'rabea']) // Ensure we exclude admin and rabea
            ->pluck('shop_name')
            ->toArray();
            
        // Also get shop names from transactions in the selected period
        $kasrShops = KasrSale::where('status', 'accepted')
            ->whereBetween('order_date', [$startDate, $endDate])
            ->distinct()
            ->pluck('shop_name')
            ->toArray();
            
        // Get shop names from completed kasr sales (will be 'rabea' but we'll use original_shop_name)
        $completedKasrShops = \App\Models\KasrSaleComplete::whereBetween('order_date', [$startDate, $endDate])
            ->distinct()
            ->pluck('original_shop_name')
            ->toArray();
            
        $soldShops = GoldItemSold::whereBetween('sold_date', [$startDate, $endDate])
            ->distinct()
            ->pluck('shop_name')
            ->toArray();
            
        // Combine and unique shop names
        $allShops = array_unique(array_merge($allShops, $kasrShops, $completedKasrShops, $soldShops));
        
        // Filter out any 'admin' or 'rabea' shop names that might have been included from transactions
        $excludedShopNames = \App\Models\User::whereIn('usertype', ['admin', 'rabea'])
            ->pluck('shop_name')
            ->toArray();
        $allShops = array_diff($allShops, $excludedShopNames);
        
        // Also manually filter out any shop containing 'rabea'
        $allShops = array_filter($allShops, function($shop) {
            return !preg_match('/(rabea|admin)/i', $shop);
        });
        
        sort($allShops); // Sort shops alphabetically
        
        $shopData = [];
        $totalBought = 0;
        $totalSold = 0;
        $totalBalance = 0;
        $runningBalance = 0; // Keep track of the running balance
        
        // Prepare shop data
        foreach ($allShops as $shop) {
            if (empty($shop)) continue; // Skip empty shop names
            
            // Get kasr items from original table for this shop
            $kasrItems = KasrItem::join('kasr_sales', 'kasr_items.kasr_sale_id', '=', 'kasr_sales.id')
                ->where('kasr_sales.status', 'accepted')
                ->where('kasr_sales.shop_name', $shop)
                ->whereBetween('kasr_sales.order_date', [$startDate, $endDate])
                ->get();
            
            // Get kasr items from completed table for this shop (using original_shop_name)
            $completedKasrItems = KasrItem::join('kasr_sales', 'kasr_items.kasr_sale_id', '=', 'kasr_sales.id')
                ->join('kasr_sales_complete', 'kasr_sales.id', '=', 'kasr_sales_complete.original_kasr_sale_id')
                ->where('kasr_sales_complete.status', 'accepted')
                ->where('kasr_sales_complete.original_shop_name', $shop)
                ->whereBetween('kasr_sales_complete.order_date', [$startDate, $endDate])
                ->get();
            
            // Combine both collections
            $allKasrItems = $kasrItems->concat($completedKasrItems);
            
            $boughtWeight = $this->normalizeWeightsTo18K($allKasrItems);
            
            // Get sold items for this shop
            $soldItems = GoldItemSold::where('shop_name', $shop)
                ->whereBetween('sold_date', [$startDate, $endDate])
                ->get();
                
            $soldWeight = $this->normalizeWeightsTo18K($soldItems, 'sold');
            
            // Calculate shop's own balance
            $shopBalance = $soldWeight - $boughtWeight;
            
            // Skip inactive shops if requested
            if ($hideInactive && $soldWeight == 0 && $boughtWeight == 0) {
                continue;
            }
            
            // Add to totals
            $totalBought += $boughtWeight;
            $totalSold += $soldWeight;
            $totalBalance += $shopBalance;
            
            // Calculate running balance (cumulative)
            $runningBalance += $shopBalance;
            
            $shopData[$shop] = [
                'bought' => $boughtWeight,
                'sold' => $soldWeight,
                'shop_balance' => $shopBalance,      // Individual shop balance
                'running_balance' => $runningBalance  // Running cumulative balance
            ];
        }
        
        // Add totals row
        $shopData['Total'] = [
            'bought' => $totalBought,
            'sold' => $totalSold,
            'shop_balance' => $totalBalance,    // Sum of individual shop balances
            'running_balance' => $totalBalance  // Final running balance should equal total balance
        ];
        
        return $shopData;
    }
} 