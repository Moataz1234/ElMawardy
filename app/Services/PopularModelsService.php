<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class PopularModelsService
{
    public function getPopularModels()
    {
        return Cache::remember('popular_models', 300, function () {
            return DB::table('gold_items_sold')
                ->select(
                    'model',
                    DB::raw('SUM(quantity) as total_quantity'),
                    DB::raw('SUM(weight) as total_weight')
                )
                ->groupBy('model')
                ->orderByDesc('total_quantity')
                ->limit(10)
                ->get();
        });
    }
}
