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
use App\Models\Talabat;

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
        $models = Models::select('model')->get(); // Get all models
        // $talabat = Talabat::select('model')->get(); // Get all talabat models
        $goldColors = GoldItem::select('gold_color')->distinct()->pluck('gold_color');
        $metalTypes = GoldItem::select('metal_type')->distinct()->pluck('metal_type');
        $metalPurities = GoldItem::select('metal_purity')->distinct()->pluck('metal_purity');
        $kinds = GoldItem::select('kind')->distinct()->pluck('kind');
    
        return view('admin.Gold.Create_form', compact(
            'shops', 
            'models', 
            // 'talabat', 
            'goldColors', 
            'metalTypes', 
            'metalPurities', 
            'kinds'
        ));
    }
    public function store(GoldItemRequest $request)
    {
        try {
            Log::info('Starting store process', ['request_data' => $request->all()]);

            // Validate the request data
            $validated = $request->validated();

            foreach ($request->shops as $shopData) {
                // Generate serial number
                $lastItem = GoldItem::orderByRaw('CAST(SUBSTRING(serial_number, 3) AS UNSIGNED) DESC')->first();
                $nextSerialNumber = $this->goldItemService->generateNextSerialNumber($lastItem);

                // Prepare gold item data
                $goldItemData = [
                    'serial_number' => $nextSerialNumber,
                    'shop_id' => $shopData['shop_id'],
                    'shop_name' => Shop::find($shopData['shop_id'])->name,
                    'kind' => $validated['kind'],
                    'gold_color' => $shopData['gold_color'],
                    'metal_type' => $validated['metal_type'],
                    'metal_purity' => $validated['metal_purity'],
                    'quantity' => $validated['quantity'],
                    'weight' => $shopData['weight'],
                    'talab' => isset($shopData['talab']) ? $shopData['talab'] : false
                ];

                // Set model or talabat based on checkbox
                if ($request->has('is_talabat')) {
                    $goldItemData['talabat'] = $validated['model'];
                    $goldItemData['model'] = null;
                    Log::info('Setting talabat model', ['talabat' => $validated['model']]);
                } else {
                    $goldItemData['model'] = $validated['model'];
                    $goldItemData['talabat'] = null;
                    Log::info('Setting regular model', ['model' => $validated['model']]);
                }

                Log::info('Creating gold item with data', ['data' => $goldItemData]);

                // Create the item
                $item = GoldItem::create($goldItemData);
                Log::info('Gold item created successfully', ['item_id' => $item->id]);
            }

            return redirect()
                ->route('gold-items.create')
                ->with('success', 'Gold items added successfully.');
        } catch (\Exception $e) {
            Log::error('Error in Store Method', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->back()
                ->with('error', 'An error occurred while adding the gold items: ' . $e->getMessage())
                ->withInput();
        }
    }
    public function edit(string $id)
    {
        $goldItem = GoldItem::findOrFail($id);
        $shops = Shop::all();

        return view('admin.Gold.Edit_form', compact('goldItem', 'shops'));
    }

    public function checkExists($model)
    {
        $exists = Models::where('model', $model)->exists();
        return response()->json(['exists' => $exists]);
    }
}
