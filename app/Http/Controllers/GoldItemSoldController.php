<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GoldItemSoldController extends Controller
{
    /**
     * Display a listing of the sold items.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sort = $request->input('sort', 'serial_number');
        $direction = $request->input('direction', 'asc');

        $goldItems = GoldItemSold::when($search, function ($query, $search) {
            return $query->where('serial_number', 'like', "%{$search}%")
                         ->orWhere('shop_name', 'like', "%{$search}%")
                         ->orWhere('kind', 'like', "%{$search}%")
                         ->orWhere('model', 'like', "%{$search}%")
                         ->orWhere('gold_color', 'like', "%{$search}%")
                         ->orWhere('stones', 'like', "%{$search}%")
                         ->orWhere('metal_type', 'like', "%{$search}%")
                         ->orWhere('metal_purity', 'like', "%{$search}%")
                         ->orWhere('source', 'like', "%{$search}%");
        })
        ->orderBy($sort, $direction)
        ->paginate(20);
        return view('admin.Gold.Gold_list_sold', compact('goldItems'));
    }

    /**
     * Show the form for editing the specified sold item.
     */
    public function edit(string $id)
    {
        $goldItemSold = GoldItemSold::findOrFail($id);
        return view('admin.Gold.Gold_edit_sold', compact('goldItemSold'));
    }

    /**
     * Update the specified sold item in storage.
     */
    public function update(Request $request, string $id)
    {
        $goldItemSold = GoldItemSold::findOrFail($id);

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
            'add_date' => 'nullable|date',
            'source' => 'nullable|string',
            'to_print' => 'nullable|boolean',
            'price' => 'nullable|numeric',
            'semi_or_no' => 'nullable|string',
            'average_of_stones' => 'nullable|numeric',
            'net_weight' => 'nullable|numeric',
            'sold_date' => 'nullable|date',
        ]);

        if ($request->hasFile('link')) {
            $image = $request->file('link');
            $imagePath = $image->store('uploads/gold_items_sold', 'public');
            $validated['link'] = $imagePath;
        }

        $goldItemSold->update($validated);

        return redirect()->route('gold-items.sold')->with('success', 'Sold gold item updated successfully.');
    }

    /**
     * Mark the specified item as sold and transfer to the sold table.
     */
    public function markAsSold(Request $request, string $id)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'payment_method' => 'required|string|max:255',
        ]);
    
        // Create a new customer entry
        $customer = Customer::create($validated);
    
        $goldItem = GoldItem::findOrFail($id);

        // Transfer data to GoldItemSold and set customer_id
        $goldItemSold = GoldItemSold::create(array_merge(
            $goldItem->toArray(),
            ['customer_id' => $customer->id]
        ));

        // Delete the item from GoldItem
        $goldItem->delete();

        return redirect()->route('gold-items.index')->with('success', 'Gold item marked as sold successfully.');
    }

    public function markAsRest(Request $request, string $id)
    {
        $goldItem = GoldItemSold::findOrFail($id);

        // Transfer data to GoldItemSold
        GoldItem::create($goldItem->toArray());

        // Delete the item from GoldItem
        $goldItem->delete();

        return redirect()->route('gold-items.sold')->with('success', 'Gold item marked as rest successfully.');
    }
}
