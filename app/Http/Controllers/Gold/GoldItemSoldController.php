<?php

namespace App\Http\Controllers\Gold;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\GoldItemSold;
use App\Models\Customer;
use App\Models\GoldItem;
use App\Services\GoldItemSoldService;

class GoldItemSoldController extends Controller
{
    protected $goldItemSoldService;

    public function __construct(GoldItemSoldService $goldItemSoldService)
    {
        $this->goldItemSoldService = $goldItemSoldService;
    }

    public function index(Request $request)
    {
        $goldItems = $this->goldItemSoldService->getGoldItemsSold($request);
        
        // Get unique values for filters
        $gold_color = GoldItemSold::distinct()->pluck('gold_color')->filter();
        $kind = GoldItemSold::distinct()->pluck('kind')->filter();

        return view('Shops.Gold.sold_index', [
            'goldItems' => $goldItems,
            'search' => $request->input('search'),
            'sort' => $request->input('sort', 'serial_number'),
            'direction' => $request->input('direction', 'asc'),
            'gold_color' => $gold_color,
            'kind' => $kind
        ]);
    }

    public function viewReports(Request $request)
    {
        $date = $request->input('date', now()->format('Y-m-d'));
        
        $reportsData = [];
        $soldItems = GoldItemSold::forDate($date)->get()->groupBy('model');

        foreach ($soldItems as $model => $items) {
            $reportsData[$model] = [
                'total_sold' => $items->count(),
                'total_weight' => $items->sum('weight'),
                'total_price' => $items->sum('price'),
                'sold_date' => $date,
                'items' => $items
            ];
        }

        return view('admin.reports.view', [
            'reportsData' => $reportsData,
            'selectedDate' => $date
        ]);
    }

    public function viewReports(Request $request)
    {
        $date = $request->input('date', now()->format('Y-m-d'));
        
        $reportsData = [];
        $soldItems = GoldItemSold::forDate($date)->get()->groupBy('model');

        foreach ($soldItems as $model => $items) {
            $reportsData[$model] = [
                'total_sold' => $items->count(),
                'total_weight' => $items->sum('weight'),
                'total_price' => $items->sum('price'),
                'sold_date' => $date,
                'items' => $items
            ];
        }

        return view('admin.reports.view', [
            'reportsData' => $reportsData,
            'selectedDate' => $date
        ]);
    }

    /**
     * Show the form for editing the specified sold item.
     */
    public function edit(string $id)
    {
        $goldItemSold = GoldItemSold::findOrFail($id);
        return view('admin.Gold.Edit_sold_form', compact('goldItemSold'));
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
