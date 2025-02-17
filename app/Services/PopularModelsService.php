<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

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

    public function getTopPerformers()
    {
        return DB::table('gold_items_sold')
            ->select(
                'model',
                DB::raw('COUNT(*) as total_sold'),
                DB::raw('SUM(weight) as total_weight'),
                DB::raw('SUM(price) as total_revenue')
            )
            ->where('sold_date', '>=', Carbon::now()->subMonths(6))
            ->groupBy('model')
            ->orderBy('total_sold', 'desc')
            ->limit(10)
            ->get();
    }
}
