<?php
// app/Http/Controllers/GoldPriceController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GoldPrice;

class GoldPriceController extends Controller
{
    // Show the form with the current gold prices
    public function create()
    {
        // Retrieve the latest gold prices
        $latestGoldPrice = GoldPrice::latest()->first();

        return view('update-prices', compact('latestGoldPrice'));
    }

    // Update the gold prices
    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'gold_buy' => 'required|numeric',
            'gold_sell' => 'required|numeric',
            'percent' => 'required|numeric',
            'dollar_price' => 'required|numeric',
            'gold_with_work' => 'required|numeric',
            'gold_in_diamond' => 'required|numeric',
            'shoghl_agnaby' => 'required|numeric',
        ]);

        GoldPrice::create($request->all());


        return redirect()->route('gold_prices.create')->with('success', 'Gold prices updated successfully.');
    }
}

