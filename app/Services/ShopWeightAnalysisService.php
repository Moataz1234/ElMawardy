<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class ShopWeightAnalysisService
{
    public function getShopWeightAnalysis()
    {
        return Cache::remember('shop_weight_analysis', 300, function () {
            return DB::table('gold_items')
                ->leftJoin('gold_items_sold', function($join) {
                    $join->on('gold_items.kind', '=', 'gold_items_sold.kind')
                        ->on('gold_items.shop_name', '=', 'gold_items_sold.shop_name');
                })
                ->select(
                    'gold_items.shop_name',
                    DB::raw('COALESCE(SUM(gold_items_sold.weight), 0) as total_weight_sold'),
                    DB::raw('SUM(gold_items.weight) as total_weight_inventory')
                )
                ->groupBy('gold_items.shop_name')
                ->get();
        });
    }
}
