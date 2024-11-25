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
                ->get();

            $totalWeightInventory = DB::table('gold_items')
                ->select('shop_name', DB::raw('SUM(weight) as total_weight_inventory'))
                ->groupBy('shop_name')
                ->get();

            return [
                'totalWeightSold' => $totalWeightSold,
                'totalWeightInventory' => $totalWeightInventory,
            ];
        });
    }
}
