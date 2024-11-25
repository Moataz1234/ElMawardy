<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class ShopWeightAnalysisService
{
    public function getTotalWeightSoldByYearAndShop(): array
    {
        return DB::table('gold_items_sold')
            ->select(DB::raw('YEAR(sold_date) as year'), 'shop_name', DB::raw('SUM(weight) as total_weight_sold'))
            ->groupBy('year', 'shop_name')
            ->orderBy('year', 'desc')
            ->orderBy('shop_name')
            ->get()
            ->groupBy('year')
            ->map(function ($yearGroup) {
                return $yearGroup->pluck('total_weight_sold', 'shop_name');
            })
            ->toArray();
    }

    public function getTotalWeightInventory(): float
    {
        return DB::table('gold_items')->sum('weight');
    }
}
