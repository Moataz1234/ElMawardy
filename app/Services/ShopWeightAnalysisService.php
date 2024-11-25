<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class ShopWeightAnalysisService
{
    public function getShopWeightAnalysis()
    {
        return Cache::remember('shop_weight_analysis', 300, function () {
            $totalWeightSold = DB::table('gold_items_sold')
                ->select('shop_name', DB::raw('SUM(weight) as total_weight_sold'))
                ->groupBy('shop_name')
                ->get()
                ->keyBy('shop_name');

            $totalWeightInventory = DB::table('gold_items')
                ->select('shop_name', DB::raw('SUM(weight) as total_weight_inventory'))
                ->groupBy('shop_name')
                ->get()
                ->keyBy('shop_name');
            
            $shopWeightAnalysis = $totalWeightInventory->map(function ($inventory, $shopName) use ($totalWeightSold) {
                return [
                    'shop_name' => $shopName,
                    'total_weight_sold' => $totalWeightSold->get($shopName)->total_weight_sold ?? 0,
                    'total_weight_inventory' => $inventory->total_weight_inventory,
                ];
            });

            return $shopWeightAnalysis->values();
        });
    }
    public function getTotalWeightSoldByYear(): array
    {
        return DB::table('gold_items_sold')
            ->select(DB::raw('YEAR(sold_date) as year'), DB::raw('SUM(weight) as total_weight_sold'))
            ->groupBy('year')
            ->orderBy('year', 'desc')
            ->get()
            ->pluck('total_weight_sold', 'year')
            ->toArray();
    }

    public function getTotalWeightInventory(): float
    {
        return DB::table('gold_items')->sum('weight');
    }
}
