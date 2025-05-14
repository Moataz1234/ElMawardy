<?php
// app/Http/Controllers/GoldPriceController.php
namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\GoldPrice;

class GoldPriceController extends Controller
{
    public function getGoldPrices()
    {
        // Get the latest gold price entry
        $latestPrice = GoldPrice::latest()->first();

        if ($latestPrice) {
            $latestPrice->makeHidden(['id', 'updated_at']);
        }

        // Return a JSON response with formatted created_at
        return response()->json([
            'data' => $latestPrice ? array_merge(
                $latestPrice->toArray(),
                ['created_at' => $latestPrice->created_at->format('Y-m-d H:i:s')]
            ) : null,
        ]);
    }

    // Show the form with the current gold prices
    public function create()
    {
        // Retrieve the latest gold prices
        $latestGoldPrice = GoldPrice::latest()->first();

        return view('admin/update-prices', compact('latestGoldPrice'));
    }

    // Update the gold prices
    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'gold_buy' => 'required|numeric',
            'gold_sell' => 'required|numeric',
            'gold_with_work' => 'required|numeric',
            'percent' => 'required|numeric',
            'dollar_price' => 'required|numeric',
            'gold_in_diamond' => 'required|numeric',
            'shoghl_agnaby' => 'required|numeric',
            'elashfoor' => 'required|numeric',
        ]);

        GoldPrice::create($request->all());

        return redirect()->route('gold_prices.create')->with('success', 'Gold prices updated successfully.');
    }
}
