<?php

namespace App\Http\Controllers\Gold;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\GoldItem;
use App\Models\Shop;
use App\Models\GoldPrice;

class GoldItemController extends Controller
{
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
        return view('admin.Gold.Inventory_list', [
            'goldItems' => $goldItems,
            'search' => $search,
            'sort' => $sort,
            'direction' => $direction
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $shops = Shop::all(); // Assuming you have a Shop model
        return view('admin.Gold.Create_form', compact('shops'));
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'shop_id' => 'required|integer',
            'kind' => 'required|string',
            'model' => 'required|string',
            'gold_color' => 'required|string',
            'metal_type' => 'required|string',
            'metal_purity' => 'required|string',
            'quantity' => 'required|integer',
            'weight' => 'required|numeric',
            'source' => 'required|string',
        ]);

        // Handle the image upload
        if ($request->hasFile('link')) {
            $image = $request->file('link');
            $imagePath = $image->store('uploads/gold_items', 'public');
            $validated['link'] = $imagePath;
        }

        // Automatically generate the next serial number
        $lastItem = GoldItem::orderByRaw('CAST(SUBSTRING(serial_number, 3) AS UNSIGNED) DESC')->first();
        if ($lastItem) {
            // Extract the numeric part from the serial number
            preg_match('/(\d+)$/', $lastItem->serial_number, $matches);
            $lastNumber = $matches ? (int)$matches[0] : 0;
            $nextSerialNumber = 'G-' . str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
        } else {
            $nextSerialNumber = 'G-000001';
        }
        // Create a new GoldItem record
        GoldItem::create([
            'serial_number' => $nextSerialNumber,
            'shop_id' => $validated['shop_id'],
            'shop_name' => Shop::find($validated['shop_id'])->name,
            'kind' => $validated['kind'],
            'model' => $validated['model'],
            'gold_color' => $validated['gold_color'],
            'metal_type' => $validated['metal_type'],
            'metal_purity' => $validated['metal_purity'],
            'quantity' => $validated['quantity'],
            'weight' => $validated['weight'],
            'source' => $validated['source'],
            'link' => $validated['link'] , // Ensure link is included
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
        return view('admin.Gold.Edit_form', compact('goldItem', 'shops'));
    }

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
            $imagePath = $image->store('gold_items', 'public');
            $validated['link'] = $imagePath;
        }

        $goldItem->update($validated);

        return redirect()->route('gold-items.index')->with('success', 'Gold item updated successfully.');
    }

    public function updatePrices(Request $request)
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'gold_buy' => 'required|numeric',
            'gold_sell' => 'required|numeric',
            'percent' => 'required|numeric',
            'dollar_price' => 'required|numeric',
            'gold_with_work' => 'required|numeric',
            'gold_in_diamond' => 'required|numeric',
            'shoghl_agnaby' => 'required|numeric',
        ]);
    
        // Create a new record in the database
        $goldPrice = GoldPrice::create([
            'gold_buy' => $validatedData['gold_buy'],
            'gold_sell' => $validatedData['gold_sell'],
            'percent' => $validatedData['percent'],
            'dollar_price' => $validatedData['dollar_price'],
            'gold_with_work' => $validatedData['gold_with_work'],
            'gold_in_diamond' => $validatedData['gold_in_diamond'],
            'shoghl_agnaby' => $validatedData['shoghl_agnaby'],
        ]);
    
        // Optionally, update all GoldItems with the latest GoldPrice 'gold_with_work' value
        GoldItem::query()->update([
            'price' => $goldPrice->gold_with_work,
        ]);
    
        return redirect()->route('prices.update.form')->with('success', 'Prices added successfully!');
    }
public function showUpdateForm()
{
    return view('admin.gold.update-all-prices');
}
    /**
     * Analyze weights of gold items and sold items.
     */
    public function analyzeWeights()
    {
        // Calculate total weight of all gold items
        $totalGoldItemWeight = GoldItem::sum('weight');

        // Calculate total weight of sold gold items for today
        $totalGoldItemSoldWeightToday = GoldItemSold::whereDate('sold_date', now()->toDateString())->sum('weight');

        return view('admin.Gold.WeightAnalysis', [
            'totalGoldItemWeight' => $totalGoldItemWeight,
            'totalGoldItemSoldWeightToday' => $totalGoldItemSoldWeightToday
        ]);
    }
}
