<?php
namespace App\Http\Controllers\Gold;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\GoldItemRequest;
use App\Services\Admin_GoldItemService;
use Illuminate\Support\Facades\Log;
use App\Models\GoldItem;
use App\Models\Shop;
use App\Models\Models;

class GoldItemController extends Controller
{
    protected $goldItemService;

    public function __construct(Admin_GoldItemService $goldItemService)
    {
        $this->goldItemService = $goldItemService;
    }

    public function index(Request $request)
    {
        $goldItems = $this->goldItemService->getGoldItems($request);

        return view('Inventory_list', [
            'goldItems' => $goldItems,
            'search' => $request->input('search'),
            'sort' => $request->input('sort', 'serial_number'),
            'direction' => $request->input('direction', 'asc')
        ]);
    }
    
    public function create()
    {
    
            $shops = Shop::all();
            $models = Models::select('model')->distinct()->get(); // Fetch unique models
            $goldColors = GoldItem::select('gold_color')->distinct()->pluck('gold_color');
            $metalTypes = GoldItem::select('metal_type')->distinct()->pluck('metal_type');
            $metalPurities = GoldItem::select('metal_purity')->distinct()->pluck('metal_purity');
            $kinds = GoldItem::select('kind')->distinct()->pluck('kind');
        
          

            return view('admin.Gold.Create_form', compact('shops', 'models', 'goldColors', 'metalTypes', 'metalPurities', 'kinds'));
        
    }

    public function store(GoldItemRequest $request)
    {
        try {
            // Validate the request data
            $validated = $request->validated();

            // Loop through the dynamic shop data
            foreach ($request->shops as $shopData) {
                // Fetch the last item to calculate the next serial number
                $lastItem = GoldItem::orderByRaw('CAST(SUBSTRING(serial_number, 3) AS UNSIGNED) DESC')->first();
                $nextSerialNumber = $this->goldItemService->generateNextSerialNumber($lastItem);
    
                // Create a new gold item record for each shop
                GoldItem::create([
                    'serial_number' => $nextSerialNumber,
                    'shop_id' => $shopData['shop_id'],
                    'shop_name' => Shop::find($shopData['shop_id'])->name,
                    'kind' => $validated['kind'],
                    'model' => $validated['model'],
                    'gold_color' => $shopData['gold_color'],
                    'metal_type' => $validated['metal_type'],
                    'metal_purity' => $validated['metal_purity'],
                    'quantity' => $validated['quantity'],
                    'weight' => $shopData['weight'],
                    'talab' => $validated['talab'],                ]);

            }
            // Redirect with success message
            return redirect()->route('gold-items.create')->with('success', 'Gold items added successfully.');
        } catch (\Exception $e) {
            // Log the error and redirect back with an error message
            Log::error('Error in Store Method', ['error' => $e->getMessage()]);
            return redirect()->back()->withInput()->with('error', 'An error occurred while adding the gold items.');
        }
    }
    public function edit(string $id)
    {
        $goldItem = GoldItem::findOrFail($id);
        $shops = Shop::all();

        return view('admin.Gold.Edit_form', compact('goldItem', 'shops'));
    }

    // public function update(Request $request, string $id)
    // {
    // $validated = $request->validate([
    //     'link' => 'nullable|file|image',
    //     'serial_number' => 'nullable|string',
    //     'shop_name' => 'nullable|string',
    //     'shop_id' => 'nullable|integer',
    //     'kind' => 'nullable|string',
    //     'model' => 'nullable|string',
    //     'talab' => 'nullable|string',
    //     'gold_color' => 'nullable|string',
    //     'stones' => 'nullable|string',
    //     'metal_type' => 'nullable|string',
    //     'metal_purity' => 'nullable|string',
    //     'quantity' => 'nullable|integer',
    //     'weight' => 'nullable|numeric',
    //     'rest_since' => 'nullable|date',
    //     'source' => 'nullable|string',
    //     'to_print' => 'nullable|boolean',
    //     'price' => 'nullable|numeric',
    //     'semi_or_no' => 'nullable|string',
    //     'average_of_stones' => 'nullable|numeric',
    //     'net_weight' => 'nullable|numeric',
    // ]);

    // $this->goldItemService->updateGoldItem($id, $validated, $request->file('link'));

    // return redirect()->route('gold-items.index')->with('success', 'Gold item updated successfully.');
    // }


}
