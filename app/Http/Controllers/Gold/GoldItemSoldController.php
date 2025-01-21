<?php

namespace App\Http\Controllers\Gold;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\GoldItemSold;
use App\Models\Models;
use App\Models\Customer;
use App\Models\GoldItem;
use App\Services\GoldItemSoldService;

use Barryvdh\DomPDF\Facade\Pdf;

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
    
    public function viewReports(Request $request)
    {
        // Get the selected date from the request or default to today
        $date = $request->input('date', now()->format('Y-m-d'));
    
        // Fetch sold items for the selected date, grouped by model, and join with the Models table
        $soldItems = GoldItemSold::whereDate('sold_date', $date)
        ->get()
        ->groupBy('model');
    
        // Calculate total items sold for the selected day
        $totalItemsSold = GoldItemSold::whereDate('sold_date', $date)->count();
    
        $reportsData = [];
    
        foreach ($soldItems as $model => $items) {
            $modelInfo = Models::where('model', $model)->first();
            $reportsData[$model] = [
                'workshop_count' => $items->where('shop_name', 'Workshop')->count(),
                'order_date' => $date,
                'gold_color' => $items->first()->gold_color,
                'source' => $modelInfo ? $modelInfo->source : $items->first()->source, // Get source from Models
                'stars' => $modelInfo ? $modelInfo->stars : $items->first()->stars, // Get source from Models
                'image_path' => $modelInfo ? $modelInfo->scanned_image : null, // Get image from Models
              'model' => $model,
                'remaining' => GoldItem::where('model', $model)->count(),
                'total_production' => GoldItem::where('model', $model)->count() + GoldItemSold::where('model', $model)->count(),
                'total_sold' => GoldItemSold::where('model', $model)->count(),
                'first_sale' => $items->min('sold_date'),
                'last_sale' => $items->max('sold_date'),
                'shop' => $items->pluck('shop_name')->unique()->implode(' / '),
                'pieces_sold_today' => $items->count(),
                'shops_data' => $this->getShopDistribution($model) // Helper method to get shop distribution
            ];
        }
    
        // Get static recipients from config
        $recipients = array_merge(
            config('reports_email.elmawardy_recipients', []),
            config('reports_email.gmail_recipients', [])
        );
        
        // Return the view for HTML display
        if ($request->has('export') && $request->input('export') === 'pdf') {
            $pdf = PDF::loadView('Admin.Reports.view', [
                'reportsData' => $reportsData,
                'selectedDate' => $date,
                'totalItemsSold' => $totalItemsSold,
                'recipients' => $recipients, // Pass recipients to the view
                'isPdf' => true // Add a flag to indicate PDF export
            ]);
            $pdf->setPaper('A4', 'landscape');
            return $pdf->stream('sales_report_' . $date . '.pdf');
        }
    
        // Return the view for HTML display
        return view('Admin.Reports.view', [
            'reportsData' => $reportsData,
            'selectedDate' => $date,
            'totalItemsSold' => $totalItemsSold,
            'recipients' => $recipients, // Pass recipients to the view
            'isPdf' => false // Add a flag to indicate HTML display
        ]);
    }
    /**
     * Helper method to get shop distribution for a specific model.
     */
    private function getShopDistribution($model)
    {
        $shops = [
            'Mohandessin Shop', 'Mall of Arabia', 'Nasr City', 'Zamalek',
            'Mall of Egypt', 'EL Guezira Shop', 'Arkan', 'District 5', 'U Venues'
        ];
    
        $shopDistribution = [];
    
        foreach ($shops as $shop) {
            $shopItems = GoldItem::where('model', $model)->where('shop_name', $shop)->get();
    
            $shopDistribution[$shop] = [
                'all_rests' => $shopItems->count(),
                'white_gold' => $shopItems->where('gold_color', 'White')->count(),
                'yellow_gold' => $shopItems->where('gold_color', 'Yellow')->count(),
                'rose_gold' => $shopItems->where('gold_color', 'Rose')->count()
            ];
        }
    
        return $shopDistribution;
    }
}
