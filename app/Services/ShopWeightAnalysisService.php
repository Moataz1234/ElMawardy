<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class ShopWeightAnalysisService
{
    public function getTotalWeightSoldByYearAndShop(): array
    {
        $excludedShops = ['EL Korba', 'Downtown', 'Downtown2', 'Mohandessin Office', 'France', 'Damgha'];

        // Get current inventory weights by shop
        $currentInventory = DB::table('gold_items')
            ->select(
                'shop_name',
                DB::raw('SUM(weight) as total_weight'),
                DB::raw('COUNT(*) as item_count')
            )
            ->whereNotIn('shop_name', $excludedShops)
            ->where('status', '!=', 'sold') // Exclude sold items
            ->groupBy('shop_name')
            ->orderBy('shop_name')
            ->get();

        // Format the data for the chart
        $inventoryData = [];
        foreach ($currentInventory as $shop) {
            if ($shop->total_weight > 0) { // Only include shops with inventory
                $inventoryData[$shop->shop_name] = round($shop->total_weight, 2);
            }
        }

        // Log for debugging
        // \Log::info('Inventory Weight Distribution:', [
        //     'total_shops' => count($inventoryData),
        //     'total_weight' => array_sum($inventoryData),
        //     'shop_breakdown' => $inventoryData,
        //     'excluded_shops' => $excludedShops
        // ]);

        return [date('Y') => $inventoryData];
    }

    public function getTotalWeightInventory(): float
    {
        // Get total weight of all current inventory
        $excludedShops = ['France', 'Damgha'];

        $totalWeight = DB::table('gold_items')
            ->whereNotIn('shop_name', $excludedShops)
            ->where('status', '!=', 'sold')
            ->sum('weight');

        // Log for debugging
        // \Log::info('Total Inventory Weight:', [
        //     'weight' => $totalWeight,
        //     'excluded_shops' => $excludedShops
        // ]);

        return round($totalWeight, 2);
    }

    public function getSalesTrends(): array
    {
        return DB::table('gold_items_sold')
            ->select(DB::raw('DATE_FORMAT(sold_date, "%Y-%m") as month'), DB::raw('SUM(weight) as total_weight_sold'))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('total_weight_sold', 'month')
            ->toArray();
    }

    public function getInventoryTurnover(): float
    {
        $totalSales = DB::table('gold_items_sold')->sum('weight');
        $averageInventory = DB::table('gold_items')->avg('weight');
        return $averageInventory ? $totalSales / $averageInventory : 0;
    }

    public function getMonthlyTrends(): array
    {
        $lastYear = Carbon::now()->subYear();
        
        return DB::table('gold_items_sold')
            ->select(
                DB::raw('DATE_FORMAT(sold_date, "%Y-%m") as month'),
                DB::raw('COUNT(*) as total_sales'),
                DB::raw('SUM(weight) as total_weight'),
                DB::raw('AVG(weight) as avg_weight')
            )
            ->where('sold_date', '>=', $lastYear)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(function ($item) {
                return [
                    'month' => $item->month,
                    'sales' => $item->total_sales,
                    'weight' => round($item->total_weight, 2),
                    'average' => round($item->avg_weight, 2)
                ];
            })
            ->toArray();
    }

    public function getInventoryTurnoverRates(): array
    {
        $excludedShops = ['Damgha', 'France'];
        $currentYear = Carbon::now()->year;

        $shops = DB::table('gold_items')
            ->select('shop_name')
            ->distinct()
            ->whereNotIn('shop_name', $excludedShops)
            ->pluck('shop_name');

        $turnoverRates = [];
        
        foreach ($shops as $shop) {
            // Calculate average inventory for the shop
            $avgInventory = DB::table('gold_items')
                ->where('shop_name', $shop)
                ->where('status', '!=', 'sold')
                ->avg('weight') ?? 0;

            // Calculate total sales for the shop in current year
            $totalSales = DB::table('gold_items_sold')
                ->where('shop_name', $shop)
                ->whereYear('sold_date', $currentYear)
                ->sum('weight') ?? 0;

            // Calculate turnover rate
            // Formula: (Total Sales / Average Inventory)
            $turnoverRate = $avgInventory > 0 ? ($totalSales / $avgInventory) : 0;

            $turnoverRates[$shop] = [
                'rate' => round($turnoverRate, 2),
                'avg_inventory' => round($avgInventory, 2),
                'total_sales' => round($totalSales, 2),
                'efficiency' => $this->calculateEfficiency($turnoverRate)
            ];
        }

        // Log for debugging
        // Log::info('Turnover Rates:', $turnoverRates);

        return $turnoverRates;
    }

    private function calculateEfficiency(float $turnoverRate): string
    {
        if ($turnoverRate >= 4) {
            return 'Excellent';
        } elseif ($turnoverRate >= 3) {
            return 'Good';
        } elseif ($turnoverRate >= 2) {
            return 'Average';
        } else {
            return 'Needs Improvement';
        }
    }

    // Helper method to cache results
    private function cacheResults(string $key, \Closure $callback, int $minutes = 60)
    {
        return Cache::remember($key, $minutes * 60, $callback);
    }
}