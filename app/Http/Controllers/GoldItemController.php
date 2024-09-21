<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GoldItem;

class GoldItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $goldItems = GoldItem::all();
        return view('admin.Gold_list', compact('goldItems'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.Gold_view');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $validated = $request->validate([
        'link' => 'string',
        'serial_number' => 'string',
        'shop_name' => 'string',
        'shop_id' => 'integer',
        'kind' => 'string',
        'model' => 'string',
        'talab' => 'string',
        'gold_color' => 'string',
        'stones' => 'nullable|string',
        'metal_type' => 'string',
        'metal_purity' => 'string',
        'quantity' => 'integer',
        'weight' => 'numeric',
        'rest_since' => 'date',
        'source' => 'string',
        'to_print' => 'nullable|boolean',
        'price' => 'numeric',
        'semi_or_no' => 'string',
        'average_of_stones' => 'nullable|numeric',
        'net_weight' => 'numeric',
    ]);

    // // Create a new GoldItem record
    // GoldItem::create([
    //     'link' => $validated['link'],
    //     'serial_number' => $validated['serial_number'],
    //     'shop_name' => $validated['shop_name'],
    //     'shop_id' => $validated['shop_id'],
    //     'kind' => $validated['kind'],
    //     'model' => $validated['model'],
    //     'talab' => $validated['talab'],
    //     'gold_color' => $validated['gold_color'],
    //     'stones' => $validated['stones'] ?? null,
    //     'metal_type' => $validated['metal_type'],
    //     'metal_purity' => $validated['metal_purity'],
    //     'quantity' => $validated['quantity'],
    //     'weight' => $validated['weight'],
    //     'rest_since' => $validated['rest_since'],
    //     'source' => $validated['source'],
    //     'to_print' => $validated['to_print'] ?? false,  // Checkbox defaults to false if not selected
    //     'price' => $validated['price'],
    //     'semi_or_no' => $validated['semi_or_no'],
    //     'average_of_stones' => $validated['average_of_stones'] ?? 0,
    //     'net_weight' => $validated['net_weight'],
    // ]);

    return redirect()->route('gold-items.create')->with('success', 'Gold item added successfully.');
}

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
