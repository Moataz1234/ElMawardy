<?php

namespace App\Http\Controllers;

use App\Models\GoldItem;
use App\Models\Outer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class OuterController extends Controller
{
    public function create($id)
    {
        $goldItem = GoldItem::findOrFail($id);
        return view('Shops.Gold.out_form', compact('goldItem'));
    }

    public function store(Request $request, $id)
    {
        $user = Auth::user();
        $goldItem = GoldItem::findOrFail($id); // Get the GoldItem using the ID

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:11',
            'reason' => 'nullable|string',
            
        ]);

        Outer::create([
            'gold_serial_number' => $goldItem->serial_number, // Use the serial number instead of ID
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone_number' => $request->phone_number,
            'reason' => $request->reason,
            'is_returned' => false,

        ]);

        return redirect()->route('gold-items.shop', ['shop' => $user->shop_name])->with('success', 'Outer data saved successfully!');
    }
    public function storeOuter(Request $request)
{
    // Create and save the outer record
    $outer = new Outer();
    $outer->gold_serial_number = $request->input('gold_serial_number');
    $outer->first_name = $request->input('first_name');
    $outer->last_name = $request->input('last_name');
    $outer->phone_number = $request->input('phone_number');
    $outer->reason = $request->input('reason');
    $outer->is_returned = false; // Assuming default
    $outer->save();

    // Get the related gold item by serial number
    $goldItem = GoldItem::where('serial_number', $outer->gold_serial_number)->first();

    return redirect()->route('gold-items.index'); // Adjust route as needed
}
public function highlightUnreturned()
{
    // Get serial numbers for gold items where is_returned is false
    $unreturnedSerialNumbers = Outer::where('is_returned', false)->pluck('gold_serial_number');

    // Retrieve gold items and pass serial numbers to the view
    $goldItems = GoldItem::all();

    return view('gold-items.index', compact('goldItems', 'unreturnedSerialNumbers'));
}

    public function returnItem($id)
{
    $outer = Outer::where('gold_serial_number', $id)->first();
    $outer->is_returned = !$outer->is_returned;
    $outer->save();

    return redirect()->back()->with('success', 'Item return status updated!');
}
}
