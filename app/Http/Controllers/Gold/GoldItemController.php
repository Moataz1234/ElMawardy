<?php

namespace App\Http\Controllers\Gold;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\GoldItem;
use App\Models\Shop;

class GoldItemController extends Controller
{
    public function shopView(Request $request, $shopId)
    {
        // Ensure the logged-in user is from the correct shop
        if (Auth::user()->shop_id != $shopId) {
            abort(403, 'Unauthorized access.');
        }
    
        // Fetch only the gold items related to the shop by shop_id
        $goldItems = GoldItem::where('shop_id', $shopId);
    
        // Apply sorting if requested
        if ($request->has('sort') && $request->has('direction')) {
            $goldItems->orderBy($request->sort, $request->direction);
        }
    
        // Apply search functionality if needed
        if ($request->has('search')) {
            $goldItems->where(function ($q) use ($request) {
                $q->where('serial_number', 'LIKE', "%{$request->search}%")
                  ->orWhere('kind', 'LIKE', "%{$request->search}%")
                  ->orWhere('model', 'LIKE', "%{$request->search}%");
            });
        }
    
        // Paginate the results
        $goldItems = $goldItems->paginate(10);
    
        return view('shops.index', compact('goldItems', 'shopId'));
    }
    public function showShopItems()
    {
        $user = Auth::user(); // Get the authenticated user
    
        // Fetch the items associated with the logged-in user's shop_name
        $goldItems = GoldItem::where('shop_name', $user->name)->paginate(20);
    
        return view('shops.index', compact('goldItems'));
    }
    /**
     * Transfer a gold item to another branch.
     */
    public function transferToBranch(Request $request, string $id)
    {
        $validated = $request->validate([
            'shop_id' => 'required|exists:shops,id',
        ]);

        $goldItem = GoldItem::findOrFail($id);
        $goldItem->shop_id = $validated['shop_id'];
        $shop = Shop::findOrFail($validated['shop_id']);
        $goldItem->shop_name = $shop->name;
        $goldItem->save();

        return redirect()->route('gold-items.index')->with('success', 'Gold item transferred successfully.');
    }
    public function showTransferForm(string $id)
    {
        $goldItem = GoldItem::findOrFail($id);
        $shops = Shop::all(); // Assuming you have a Shop model
        return view('admin.Gold.Gold_transfer', compact('goldItem', 'shops'));
    }

    /**
     * Add new products from the factory to a branch.
     */
    public function addFromFactory(Request $request)
    {
        $validated = $request->validate([
            'link' => 'nullable|string',
            'serial_number' => 'required|string',
            'shop_name' => 'required|string',
            'shop_id' => 'required|integer',
            'kind' => 'required|string',
            'model' => 'required|string',
            'talab' => 'required|string',
            'gold_color' => 'required|string',
            'stones' => 'nullable|string',
            'metal_type' => 'required|string',
            'metal_purity' => 'required|string',
            'quantity' => 'required|integer',
            'weight' => 'required|numeric',
            'rest_since' => 'required|date',
            'source' => 'required|string',
            'to_print' => 'nullable|boolean',
            'price' => 'required|numeric',
            'semi_or_no' => 'required|string',
            'average_of_stones' => 'nullable|numeric',
            'net_weight' => 'required|numeric',
        ]);


        GoldItem::create($validated);

        return redirect()->route('gold-items.index')->with('success', 'New product added from factory successfully.');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sort = $request->input('sort', 'serial_number');
        $direction = $request->input('direction', 'asc');

        $goldItems = GoldItem::with('shop')
            // ->where('shop_name', auth()->user()->name)
            ->when($search, function ($query, $search) {
                return $query->where('serial_number', 'like', "%{$search}%")
                ->orWhereHas('shop', function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%");
                                })
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
        return view('admin.Gold.Gold_list', compact('goldItems'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $shops = Shop::all(); // Assuming you have a Shop model
        return view('admin.Gold.Gold_view', compact('shops'));
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

    if ($request->hasFile('link')) {
        // Handle file upload and store the file
        $image = $request->file('link');
        $imagePath = $image->store('uploads/gold_items', 'public');
        $validated['link'] = $imagePath;
    }

    // Create a new GoldItem record
    GoldItem::create([
        'link' => $validated['link'] ?? null,
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
        $shops = Shop::all(); // Assuming you have a Shop model
        return view('admin.Gold.Gold_edit', compact('goldItem', 'shops'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $goldItem = GoldItem::findOrFail($id);

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

        if ($request->hasFile('link')) {
            $image = $request->file('link');
            $imagePath = $image->store('uploads/gold_items', 'public');
            $validated['link'] = $imagePath;
        }

        $goldItem->update($validated);

        return redirect()->route('gold-items.index')->with('success', 'Gold item updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}