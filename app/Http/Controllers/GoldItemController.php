<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GoldItem;

class GoldItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $goldItems = GoldItem::when($search, function ($query, $search) {
            return $query->where('serial_number', 'like', "%{$search}%")
                         ->orWhere('shop_name', 'like', "%{$search}%")
                         ->orWhere('kind', 'like', "%{$search}%")
                         ->orWhere('model', 'like', "%{$search}%")
                         ->orWhere('gold_color', 'like', "%{$search}%")
                         ->orWhere('stones', 'like', "%{$search}%")
                         ->orWhere('metal_type', 'like', "%{$search}%")
                         ->orWhere('metal_purity', 'like', "%{$search}%")
                         ->orWhere('source', 'like', "%{$search}%");
        })->paginate(36);
        return view('admin.Gold.Gold_list', compact('goldItems'));
    }   

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.Gold.Gold_view');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $validated = $request->validate([
        'link' => 'nullable|string',
        'serial_number' => 'nullable|string',
        'shop_name' => 'nullable|string',
        'shop_id' => 'nullable|integer',
        'kind' => 'nullable|string',
        'model' => 'nullable|string',
        'talab' => 'nullable|string',
        'gold_color' => 'nullable|string',
        'stones' => 'nullable|string',
        'metal_type' => 'nullable|string',
        'metal_purity' => 'nullable|string',
        'quantity' => 'nullable|integer',
        'weight' => 'nullable|numeric',
        'rest_since' => 'nullable|date',
        'source' => 'nullable|string',
        'to_print' => 'nullable|boolean',
        'price' => 'nullable|numeric',
        'semi_or_no' => 'nullable|string',
        'average_of_stones' => 'nullable|numeric',
        'net_weight' => 'nullable|numeric',
    ]);

    // Create a new GoldItem record
    GoldItem::create([
        'link' => $validated['link'],
        'serial_number' => $validated['serial_number'],
        'shop_name' => $validated['shop_name'],
        'shop_id' => $validated['shop_id'],
        'kind' => $validated['kind'],
        'model' => $validated['model'],
        'talab' => $validated['talab'],
        'gold_color' => $validated['gold_color'],
        'stones' => $validated['stones'] ?? null,
        'metal_type' => $validated['metal_type'],
        'metal_purity' => $validated['metal_purity'],
        'quantity' => $validated['quantity'],
        'weight' => $validated['weight'],
        'rest_since' => $validated['rest_since'],
        'source' => $validated['source'],
        'to_print' => $validated['to_print'] ?? false,  // Checkbox defaults to false if not selected
        'price' => $validated['price'],
        'semi_or_no' => $validated['semi_or_no'],
        'average_of_stones' => $validated['average_of_stones'] ?? 0,
        'net_weight' => $validated['net_weight'],
    ]);
    if ($request->hasFile('link')) {
        // Handle file upload and store the file
        $image = $request->file('link');
        $imagePath = $image->store('uploads/gold_items', 'public');
        $validatedData['link'] = $imagePath;
    }

    return redirect()->route('gold-items.create')->with('success', 'Gold item added successfully.');
}

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        
        $goldItem = GoldItem::findOrFail($id);
        return view('admin.Gold.Gold_edit', compact('goldItem'));
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
