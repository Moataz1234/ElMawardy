<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GoldItemSold;
use App\Models\GoldPoundSold;
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
        // Get filter parameter - default to today if not provided
        $reportDate = $request->input('report_date') ? Carbon::parse($request->input('report_date')) : Carbon::today();
        $startDate = $reportDate->copy()->startOfDay();
        $endDate = $reportDate->copy()->endOfDay();
        $hideInactive = $request->has('hide_inactive');
        
        // Get selected month for monthly report
        $selectedMonth = $request->input('month') ? Carbon::parse($request->input('month')) : Carbon::now();
        $monthStartDate = $selectedMonth->copy()->startOfMonth();
        $monthEndDate = $selectedMonth->copy()->endOfMonth();
        $hideInactiveMonthly = $request->has('hide_inactive_monthly');
        
        // Get KasrItems from REGULAR SALES only (not completed)
        // This query gets regular kasr items excluding those that have been completed
        $regularKasrItems = KasrItem::join('kasr_sales', 'kasr_items.kasr_sale_id', '=', 'kasr_sales.id')
            ->leftJoin('kasr_sales_complete', 'kasr_sales.id', '=', 'kasr_sales_complete.original_kasr_sale_id')
            ->whereNull('kasr_sales_complete.id') // Only those WITHOUT a completed record
            ->where('kasr_sales.status', 'accepted')
            ->whereDate('kasr_sales.order_date', $reportDate)
            ->select('kasr_items.*', 'kasr_sales.shop_name', 'kasr_sales.offered_price')
            ->get();
            
        // Get items from COMPLETED SALES only
        $completedSaleItems = KasrItem::join('kasr_sales', 'kasr_items.kasr_sale_id', '=', 'kasr_sales.id')
            ->join('kasr_sales_complete', 'kasr_sales.id', '=', 'kasr_sales_complete.original_kasr_sale_id')
            ->where('kasr_sales_complete.status', 'accepted')
            ->whereDate('kasr_sales_complete.order_date', $reportDate)
            ->select('kasr_items.*', 'kasr_sales_complete.original_shop_name as shop_name', 'kasr_sales_complete.offered_price')
            ->get();
            
        // Combine the non-overlapping sets
        $allKasrItems = $regularKasrItems->concat($completedSaleItems);
        
        // Calculate total bought weight and price
        $totalBoughtWeight = $this->normalizeWeightsTo18K($allKasrItems);
        $totalBoughtPrice = $this->calculateTotalBoughtPrice($allKasrItems);
        
        // Query for Gold Items Sold and Gold Pounds Sold
        $goldItemsSold = GoldItemSold::whereDate('sold_date', $reportDate)->get();
        $goldPoundsSold = GoldPoundSold::with('goldPound')
            ->whereDate('created_at', $reportDate)
            ->get();
        
        // Calculate total sold weight and price
        $goldItemsSoldWeight = $this->normalizeWeightsTo18K($goldItemsSold, 'sold');
        $goldPoundsSoldWeight = $this->normalizeGoldPoundsWeightTo18K($goldPoundsSold);
        $totalSoldWeight = $goldItemsSoldWeight + $goldPoundsSoldWeight;
        
        $goldItemsSoldPrice = $goldItemsSold->sum('price');
        $goldPoundsSoldPrice = $goldPoundsSold->sum('price');
        $totalSoldPrice = $goldItemsSoldPrice + $goldPoundsSoldPrice;
        
        // Calculate balance
        $balance = $totalBoughtWeight - $totalSoldWeight;
        
        // Get weight by purity
        $boughtWeightByPurity = $this->getWeightByPurity($allKasrItems);
        $soldWeightByPurity = $this->getCombinedSoldWeightByPurity($goldItemsSold, $goldPoundsSold);
        
        // Get monthly data for chart (still keep this for historical view)
        $monthlyData = $this->getMonthlyData($startDate->copy()->subMonths(6), $endDate);
        
        // Get shop-based report data
        $shopReportData = $this->getShopReportData($reportDate, $hideInactive);
        
        // Get monthly report data
        $monthlyReportData = $this->getMonthlyReportData($monthStartDate, $monthEndDate, $hideInactiveMonthly);
        
        return view('admin.Reports.gold-balance', compact(
            'totalBoughtWeight', 
            'totalSoldWeight', 
            'balance', 
            'totalBoughtPrice',
            'totalSoldPrice',
            'boughtWeightByPurity', 
            'soldWeightByPurity',
            'monthlyData',
            'reportDate',
            'shopReportData',
            'hideInactive',
            'selectedMonth',
            'monthlyReportData',
            'hideInactiveMonthly'
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
        
        // Bought monthly data - regular sales (excluding completed ones)
        $regularBoughtMonthly = DB::table('kasr_items')
            ->join('kasr_sales', 'kasr_items.kasr_sale_id', '=', 'kasr_sales.id')
            ->leftJoin('kasr_sales_complete', 'kasr_sales.id', '=', 'kasr_sales_complete.original_kasr_sale_id')
            ->whereNull('kasr_sales_complete.id') // Only those WITHOUT a completed record
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
            
        // Bought monthly data - completed sales
        $completedBoughtMonthly = DB::table('kasr_items')
            ->join('kasr_sales', 'kasr_items.kasr_sale_id', '=', 'kasr_sales.id')
            ->join('kasr_sales_complete', 'kasr_sales.id', '=', 'kasr_sales_complete.original_kasr_sale_id')
            ->where('kasr_sales_complete.status', 'accepted')
            ->whereBetween('kasr_sales_complete.order_date', [$startDate, $endDate])
            ->select(
                DB::raw('YEAR(kasr_sales_complete.order_date) as year'),
                DB::raw('MONTH(kasr_sales_complete.order_date) as month'),
                DB::raw('SUM(kasr_items.weight) as total_weight'),
                'kasr_items.metal_purity'
            )
            ->groupBy('year', 'month', 'kasr_items.metal_purity')
            ->get();
            
        // Combine bought data
        $boughtMonthly = $regularBoughtMonthly->concat($completedBoughtMonthly);
            
        // Sold monthly data (GoldItemSold)
        $goldItemsSoldMonthly = GoldItemSold::whereBetween('sold_date', [$startDate, $endDate])
            ->select(
                DB::raw('YEAR(sold_date) as year'),
                DB::raw('MONTH(sold_date) as month'),
                DB::raw('SUM(weight) as total_weight'),
                'metal_purity'
            )
            ->groupBy('year', 'month', 'metal_purity')
            ->get();
            
        // Sold monthly data (GoldPoundSold)
        $goldPoundsSoldMonthly = DB::table('gold_pounds_sold')
            ->join('gold_pounds', 'gold_pounds_sold.gold_pound_id', '=', 'gold_pounds.id')
            ->whereBetween('gold_pounds_sold.created_at', [$startDate, $endDate])
            ->select(
                DB::raw('YEAR(gold_pounds_sold.created_at) as year'),
                DB::raw('MONTH(gold_pounds_sold.created_at) as month'),
                DB::raw('SUM(gold_pounds.weight) as total_weight'),
                'gold_pounds.purity as metal_purity'
            )
            ->groupBy('year', 'month', 'gold_pounds.purity')
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
        
        // Process gold items sold data
        foreach ($goldItemsSoldMonthly as $record) {
            $yearMonth = sprintf('%04d-%02d', $record->year, $record->month);
            if (isset($monthlyChartData[$yearMonth])) {
                // Extract numeric value from purity
                $purityValue = (int) preg_replace('/[^0-9]/', '', $record->metal_purity);
                // Normalize to 18K
                $normalizedWeight = $record->total_weight * ($purityValue / 18);
                $monthlyChartData[$yearMonth]['sold'] += $normalizedWeight;
            }
        }
        
        // Process gold pounds sold data
        foreach ($goldPoundsSoldMonthly as $record) {
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
     * Calculate total price for bought items (kasr)
     * 
     * @param \Illuminate\Support\Collection $kasrItems
     * @return float
     */
    private function calculateTotalBoughtPrice($kasrItems)
    {
        $total = 0;
        $processedSales = [];
        
        foreach ($kasrItems as $item) {
            $saleId = $item->kasr_sale_id;
            
            // Only add the offered_price once per sale (not per item)
            if (!in_array($saleId, $processedSales)) {
                $total += $item->offered_price ?? 0;
                $processedSales[] = $saleId;
            }
        }
        
        return $total;
    }
    
    /**
     * Get shop-based report data
     * 
     * @param Carbon $reportDate
     * @param bool $hideInactive
     * @return array
     */
    private function getShopReportData($reportDate, $hideInactive = false)
    {
        // Get all shop names from users table - only 'user' type, exclude 'admin' and 'rabea'
        $allShops = \App\Models\User::where('usertype', 'user')
            ->whereNotIn('usertype', ['admin', 'rabea']) 
            ->pluck('shop_name')
            ->toArray();
            
        // Also get shop names from transactions in the selected period
        $kasrShops = KasrSale::where('status', 'accepted')
            ->whereDate('order_date', $reportDate)
            ->distinct()
            ->pluck('shop_name')
            ->toArray();
            
        // Get shop names from completed kasr sales
        $completedKasrShops = \App\Models\KasrSaleComplete::whereDate('order_date', $reportDate)
            ->distinct()
            ->pluck('original_shop_name')
            ->toArray();
            
        $soldShops = GoldItemSold::whereDate('sold_date', $reportDate)
            ->distinct()
            ->pluck('shop_name')
            ->toArray();
            
        // Get shop names from gold pounds sold
        $poundSoldShops = GoldPoundSold::whereDate('created_at', $reportDate)
            ->distinct()
            ->pluck('shop_name')
            ->toArray();
            
        // Combine and unique shop names
        $allShops = array_unique(array_merge($allShops, $kasrShops, $completedKasrShops, $soldShops, $poundSoldShops));
        
        // Filter out any 'admin' or 'rabea' shop names
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
        $totalBoughtPrice = 0;
        $totalSoldPrice = 0;
        $runningBalance = 0; // Keep track of the running balance
        
        // Prepare shop data
        foreach ($allShops as $shop) {
            if (empty($shop)) continue; // Skip empty shop names
            
            // Get regular kasr items for this shop (excluding completed ones)
            $regularKasrItems = KasrItem::join('kasr_sales', 'kasr_items.kasr_sale_id', '=', 'kasr_sales.id')
                ->leftJoin('kasr_sales_complete', 'kasr_sales.id', '=', 'kasr_sales_complete.original_kasr_sale_id')
                ->whereNull('kasr_sales_complete.id') // Only those WITHOUT a completed record
                ->where('kasr_sales.status', 'accepted')
                ->where('kasr_sales.shop_name', $shop)
                ->whereDate('kasr_sales.order_date', $reportDate)
                ->select('kasr_items.*', 'kasr_sales.offered_price')
                ->get();
            
            // Get completed kasr items for this shop
            $completedKasrItems = KasrItem::join('kasr_sales', 'kasr_items.kasr_sale_id', '=', 'kasr_sales.id')
                ->join('kasr_sales_complete', 'kasr_sales.id', '=', 'kasr_sales_complete.original_kasr_sale_id')
                ->where('kasr_sales_complete.status', 'accepted')
                ->where('kasr_sales_complete.original_shop_name', $shop)
                ->whereDate('kasr_sales_complete.order_date', $reportDate)
                ->select('kasr_items.*', 'kasr_sales_complete.offered_price')
                ->get();
            
            // Combine both collections
            $allKasrItems = $regularKasrItems->concat($completedKasrItems);
            
            $boughtWeight = $this->normalizeWeightsTo18K($allKasrItems);
            $boughtPrice = $this->calculateTotalBoughtPrice($allKasrItems);
            
            // Get sold items for this shop (Gold Items)
            $goldItemsSold = GoldItemSold::where('shop_name', $shop)
                ->whereDate('sold_date', $reportDate)
                ->get();
                
            // Get sold items for this shop (Gold Pounds)
            $goldPoundsSold = GoldPoundSold::with('goldPound')
                ->where('shop_name', $shop)
                ->whereDate('created_at', $reportDate)
                ->get();
                
            $goldItemsSoldWeight = $this->normalizeWeightsTo18K($goldItemsSold, 'sold');
            $goldPoundsSoldWeight = $this->normalizeGoldPoundsWeightTo18K($goldPoundsSold);
            $soldWeight = $goldItemsSoldWeight + $goldPoundsSoldWeight;
            
            $goldItemsSoldPrice = $goldItemsSold->sum('price');
            $goldPoundsSoldPrice = $goldPoundsSold->sum('price');
            $soldPrice = $goldItemsSoldPrice + $goldPoundsSoldPrice;
            
            // Calculate shop's own balance
            $shopBalance = $boughtWeight - $soldWeight;
            
            // Skip inactive shops if requested
            if ($hideInactive && $soldWeight == 0 && $boughtWeight == 0) {
                continue;
            }
            
            // Add to totals
            $totalBought += $boughtWeight;
            $totalSold += $soldWeight;
            $totalBalance += $shopBalance;
            $totalBoughtPrice += $boughtPrice;
            $totalSoldPrice += $soldPrice;
            
            // Calculate running balance (cumulative)
            $runningBalance += $shopBalance;
            
            $shopData[$shop] = [
                'bought' => $boughtWeight,
                'sold' => $soldWeight,
                'shop_balance' => $shopBalance,      // Individual shop balance
                'running_balance' => $runningBalance,  // Running cumulative balance
                'bought_price' => $boughtPrice,
                'sold_price' => $soldPrice
            ];
        }
        
        // Add totals row
        $shopData['Total'] = [
            'bought' => $totalBought,
            'sold' => $totalSold,
            'shop_balance' => $totalBalance,    // Sum of individual shop balances
            'running_balance' => $totalBalance,  // Final running balance should equal total balance
            'bought_price' => $totalBoughtPrice,
            'sold_price' => $totalSoldPrice
        ];
        
        return $shopData;
    }
    
    /**
     * Get monthly report data for a specific month
     * 
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return array
     */
    private function getMonthlyReportData($startDate, $endDate, $hideInactive = false)
    {
        // Get regular kasr items for the month
        $regularKasrItems = KasrItem::join('kasr_sales', 'kasr_items.kasr_sale_id', '=', 'kasr_sales.id')
            ->leftJoin('kasr_sales_complete', 'kasr_sales.id', '=', 'kasr_sales_complete.original_kasr_sale_id')
            ->whereNull('kasr_sales_complete.id')
            ->where('kasr_sales.status', 'accepted')
            ->whereBetween('kasr_sales.order_date', [$startDate, $endDate])
            ->select('kasr_items.*', 'kasr_sales.shop_name', 'kasr_sales.offered_price')
            ->get();
            
        // Get completed kasr items for the month
        $completedKasrItems = KasrItem::join('kasr_sales', 'kasr_items.kasr_sale_id', '=', 'kasr_sales.id')
            ->join('kasr_sales_complete', 'kasr_sales.id', '=', 'kasr_sales_complete.original_kasr_sale_id')
            ->where('kasr_sales_complete.status', 'accepted')
            ->whereBetween('kasr_sales_complete.order_date', [$startDate, $endDate])
            ->select('kasr_items.*', 'kasr_sales_complete.original_shop_name as shop_name', 'kasr_sales_complete.offered_price')
            ->get();
            
        // Combine both collections
        $allKasrItems = $regularKasrItems->concat($completedKasrItems);
        
        // Get sold items for the month
        $goldItemsSold = GoldItemSold::whereBetween('sold_date', [$startDate, $endDate])->get();
        $goldPoundsSold = GoldPoundSold::with('goldPound')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();
        
        // Calculate totals
        $totalBoughtWeight = $this->normalizeWeightsTo18K($allKasrItems);
        $goldItemsSoldWeight = $this->normalizeWeightsTo18K($goldItemsSold, 'sold');
        $goldPoundsSoldWeight = $this->normalizeGoldPoundsWeightTo18K($goldPoundsSold);
        $totalSoldWeight = $goldItemsSoldWeight + $goldPoundsSoldWeight;
        $totalBalance = $totalBoughtWeight - $totalSoldWeight;
        $totalBoughtPrice = $this->calculateTotalBoughtPrice($allKasrItems);
        $goldItemsSoldPrice = $goldItemsSold->sum('price');
        $goldPoundsSoldPrice = $goldPoundsSold->sum('price');
        $totalSoldPrice = $goldItemsSoldPrice + $goldPoundsSoldPrice;
        
        // Get weight by purity
        $boughtWeightByPurity = $this->getWeightByPurity($allKasrItems);
        $soldWeightByPurity = $this->getCombinedSoldWeightByPurity($goldItemsSold, $goldPoundsSold);
        
        // Get all unique shop names
        $uniqueTransactionShops = array_unique(array_merge(
            $allKasrItems->pluck('shop_name')->toArray(),
            $goldItemsSold->pluck('shop_name')->toArray(),
            $goldPoundsSold->pluck('shop_name')->toArray()
        ));

        // Get all shop names from users table - only 'user' type, exclude 'admin' and 'rabea'
        $allRegisteredShops = \App\Models\User::where('usertype', 'user')
            ->whereNotIn('usertype', ['admin', 'rabea'])
            ->pluck('shop_name')
            ->toArray();
        
        // Also manually filter out any shop containing 'rabea' or 'admin' from registered shops
        $allRegisteredShops = array_filter($allRegisteredShops, function($shop) {
            return !preg_match('/(rabea|admin)/i', $shop);
        });

        // Combine and unique shop names - ensuring all registered shops are included
        $allShops = array_unique(array_merge($allRegisteredShops, $uniqueTransactionShops));
        sort($allShops); // Sort shops alphabetically
        
        // Calculate shop-based data
        $monthlyShopData = [];
        $totalBought = 0;
        $totalSold = 0;
        $totalBoughtPriceShops = 0;
        $totalSoldPriceShops = 0;
        
        foreach ($allShops as $shop) {
            if (empty($shop)) continue;
            
            // Get bought items for this shop
            $shopBoughtItems = $allKasrItems->where('shop_name', $shop);
            $boughtWeight = $this->normalizeWeightsTo18K($shopBoughtItems);
            $boughtPrice = $this->calculateTotalBoughtPrice($shopBoughtItems);
            
            // Get sold items for this shop
            $shopGoldItemsSold = $goldItemsSold->where('shop_name', $shop);
            $shopGoldPoundsSold = $goldPoundsSold->where('shop_name', $shop);
            
            $goldItemsSoldWeight = $this->normalizeWeightsTo18K($shopGoldItemsSold, 'sold');
            $goldPoundsSoldWeight = $this->normalizeGoldPoundsWeightTo18K($shopGoldPoundsSold);
            $soldWeight = $goldItemsSoldWeight + $goldPoundsSoldWeight;
            
            $goldItemsSoldPrice = $shopGoldItemsSold->sum('price');
            $goldPoundsSoldPrice = $shopGoldPoundsSold->sum('price');
            $soldPrice = $goldItemsSoldPrice + $goldPoundsSoldPrice;
            
            // Calculate shop balance
            $shopBalance = $boughtWeight - $soldWeight;
            
            // Skip inactive shops if requested
            if ($hideInactive && $soldWeight == 0 && $boughtWeight == 0) {
                continue;
            }
            
            // Add to totals
            $totalBought += $boughtWeight;
            $totalSold += $soldWeight;
            $totalBoughtPriceShops += $boughtPrice;
            $totalSoldPriceShops += $soldPrice;
            
            $monthlyShopData[$shop] = [
                'bought' => $boughtWeight,
                'sold' => $soldWeight,
                'balance' => $shopBalance,
                'bought_price' => $boughtPrice,
                'sold_price' => $soldPrice
            ];
        }
        
        // Add totals row
        $monthlyShopData['Total'] = [
            'bought' => $totalBought,
            'sold' => $totalSold,
            'balance' => $totalBought - $totalSold,
            'bought_price' => $totalBoughtPriceShops,
            'sold_price' => $totalSoldPriceShops
        ];
        
        return [
            'total_bought' => $totalBoughtWeight,
            'total_sold' => $totalSoldWeight,
            'total_balance' => $totalBalance,
            'total_bought_price' => $totalBoughtPrice,
            'total_sold_price' => $totalSoldPrice,
            'bought_by_purity' => $boughtWeightByPurity,
            'sold_by_purity' => $soldWeightByPurity,
            'shop_data' => $monthlyShopData
        ];
    }
    
    /**
     * Normalize gold pounds weight to 18K purity
     * 
     * @param \Illuminate\Support\Collection $goldPoundsSold
     * @return float
     */
    private function normalizeGoldPoundsWeightTo18K($goldPoundsSold)
    {
        $totalNormalizedWeight = 0;
        
        foreach ($goldPoundsSold as $poundSold) {
            if (!$poundSold->goldPound) {
                continue;
            }
            
            $weight = $poundSold->goldPound->weight;
            $purity = $poundSold->goldPound->purity;
            
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
     * Get combined weight by purity for both gold items and gold pounds sold
     * 
     * @param \Illuminate\Support\Collection $goldItemsSold
     * @param \Illuminate\Support\Collection $goldPoundsSold
     * @return array
     */
    private function getCombinedSoldWeightByPurity($goldItemsSold, $goldPoundsSold)
    {
        $weightByPurity = [];
        
        // Process gold items sold
        foreach ($goldItemsSold as $item) {
            $weight = $item->weight;
            $purity = $item->metal_purity;
            
            // Skip items with no purity
            if (!$purity) {
                continue;
            }
            
            if (!isset($weightByPurity[$purity])) {
                $weightByPurity[$purity] = 0;
            }
            
            $weightByPurity[$purity] += $weight;
        }
        
        // Process gold pounds sold
        foreach ($goldPoundsSold as $poundSold) {
            if (!$poundSold->goldPound) {
                continue;
            }
            
            $weight = $poundSold->goldPound->weight;
            $purity = $poundSold->goldPound->purity;
            
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
}