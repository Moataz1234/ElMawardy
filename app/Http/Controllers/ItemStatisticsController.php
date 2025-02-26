<?php

namespace App\Http\Controllers;

use App\Models\GoldItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ItemStatisticsController extends Controller
{
    public function index()
    {
        $shopName = Auth::user()->shop_name;
        
        // Get statistics only for the logged-in user's shop
        $statistics = GoldItem::select(
            'kind',
            DB::raw('COUNT(*) as total_items'),
            DB::raw('SUM(weight) as total_weight')
        )
        ->where('shop_name', $shopName)
        ->whereNotIn('status', ['sold', 'deleted'])
        ->groupBy('kind')
        ->orderBy('kind')
        ->get();

        return view('statistics.items_count', compact('statistics', 'shopName'));
    }
} 