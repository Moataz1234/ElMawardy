<?php

namespace App\Http\Controllers;

use App\Models\Talabat;
use App\Models\GoldItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TalabatController extends Controller
{
    public function index(Request $request)
    {
        $query = Talabat::query();
    
        if ($request->has('search')) {
            $query->where('model', 'like', '%' . $request->search . '%');
        }
    
        if ($request->has('sort')) {
            $query->orderBy($request->sort, $request->get('direction', 'asc'));
        }
    
        $talabat = $query->paginate(20);
    
        return view('admin.Gold.talabat', compact('talabat'));
    }

    public function create()
    {
        return view('admin.Gold.create_talabat');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'model' => 'required|string|max:255|unique:talabat,model',
            'stars' => 'string|max:255|nullable',
            'source' => 'string|max:255|nullable',
            'first_production' => 'date|nullable',
            'semi_or_no' => 'string|max:255|nullable',
            'average_of_stones' => 'numeric|nullable',
            'scanned_image' => 'image|mimes:jpeg,png,jpg,gif|max:2048|nullable',
            // 'website_image' => 'image|mimes:jpeg,png,jpg,gif|max:2048|nullable',
        ]);

        // Generate SKU from model number
        preg_match('/^(\d+)-(\d+)(?:-(\w+))?$/', $request->model, $matches);

        // if (count($matches) >= 3) {
        //     $prefix = $matches[1];
        //     $mainPart = $matches[2];
        //     $suffix = $matches[3] ?? '';
        //     $sku = 'G' . $prefix . $mainPart . $suffix;
        // } else {
        //     $sku = 'G' . str_pad(substr($request->model, -4), 4, '0', STR_PAD_LEFT);
        // }

        // $validatedData['SKU'] = $sku;

        if ($request->hasFile('scanned_image')) {
            $scannedImagePath = $request->file('scanned_image')->store('Gold_catalog', 'public');
            $validatedData['scanned_image'] = $scannedImagePath;
        }

        // if ($request->hasFile('website_image')) {
        //     $websiteImagePath = $request->file('website_image')->store('talabat/website', 'public');
        //     $validatedData['website_image'] = $websiteImagePath;
        // }

        Talabat::create($validatedData);

        return redirect()->route('gold-items.create')->with('success', 'Talabat added successfully.');
    }

    public function edit(Talabat $talabat)
    {
        return view('admin.Gold.edit_talabat', compact('talabat'));
    }

    public function update(Request $request, Talabat $talabat)
    {
        $validatedData = $request->validate([
            'model' => 'required|string|max:255|unique:talabat,model,' . $talabat->id,
            'stars' => 'string|max:255|nullable',
            'source' => 'string|max:255|nullable',
            'scanned_image' => 'image|mimes:jpeg,png,jpg,gif|max:2048|nullable',
            // 'website_image' => 'image|mimes:jpeg,png,jpg,gif|max:2048|nullable',
        ]);

        // preg_match('/^(\d+)-(\d+)(?:-(\w+))?$/', $request->model, $matches);

        // if (count($matches) >= 3) {
        //     $prefix = $matches[1];
        //     $mainPart = $matches[2];
        //     $suffix = $matches[3] ?? '';
        //     $sku = 'G' . $prefix . $mainPart . $suffix;
        // } else {
        //     $sku = 'G' . str_pad(substr($request->model, -4), 4, '0', STR_PAD_LEFT);
        // }
    
        // $validatedData['SKU'] = $sku;
    
        if ($request->hasFile('scanned_image')) {
            $scannedImagePath = $request->file('scanned_image')->store('talabat/scanned', 'public');
            $validatedData['scanned_image'] = $scannedImagePath;
        }

        // if ($request->hasFile('website_image')) {
        //     $websiteImagePath = $request->file('website_image')->store('talabat/website', 'public');
        //     $validatedData['website_image'] = $websiteImagePath;
        // }

        $talabat->update($validatedData);

        return redirect()->route('talabat.index')->with('success', 'Talabat updated successfully.');
    }

    public function destroy(Talabat $talabat)
    {
        if ($talabat->scanned_image) {
            Storage::disk('public')->delete($talabat->scanned_image);
        }
        // if ($talabat->website_image) {
        //     Storage::disk('public')->delete($talabat->website_image);
        // }

        $talabat->delete();

        return redirect()->route('talabat.index')->with('success', 'Talabat deleted successfully.');
    }

    public function getTalabatDetails(Request $request)
    {
        $model = $request->input('model');

        $talabatDetails = Talabat::where('model', $model)->first();
    
        $items = GoldItem::with('shop')
            ->where('model', $model)
            ->whereHas('shop')
            ->get()
            ->map(function ($item) {
                return [
                    'serial_number' => $item->serial_number,
                    'shop_name' => $item->shop->name,
                    'weight' => $item->weight,
                    'gold_color' => $item->gold_color
                ];
            });
    
        return response()->json([
            'items' => $items,
            'talabatDetails' => $talabatDetails ? [
                'scanned_image' => $talabatDetails->scanned_image,
            ] : null
        ]);
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Talabat;
use App\Models\GoldItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Shop;

class TalabatController extends Controller
{
    public function create()
    {
        $shops = Shop::all();
        $talabat = Talabat::select('model')->distinct()->get();
        $goldColors = GoldItem::select('gold_color')->distinct()->pluck('gold_color');
        $metalTypes = GoldItem::select('metal_type')->distinct()->pluck('metal_type');
        $metalPurities = GoldItem::select('metal_purity')->distinct()->pluck('metal_purity');

        return view('admin.Gold.Create_form_talabat', compact('shops', 'talabat', 'goldColors', 'metalTypes', 'metalPurities'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'model' => 'required|string|max:255',
                'kind' => 'required|string|max:255',
                'metal_type' => 'required|string|max:255',
                'metal_purity' => 'required|string|max:255',
                'quantity' => 'required|integer|min:1',
                'shops.*.shop_id' => 'required|integer|exists:shops,id',
                'shops.*.gold_color' => 'required|string|max:255',
                'shops.*.weight' => 'required|numeric|min:0',
                'shops.*.talab' => 'boolean',
            ]);

            foreach ($request->shops as $shopData) {
                $lastItem = GoldItem::orderByRaw('CAST(SUBSTRING(serial_number, 3) AS UNSIGNED) DESC')->first();
                $nextSerialNumber = $this->generateNextSerialNumber($lastItem);

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
                    'talab' => $shopData['talab'] ?? 0,
                ]);
            }

            return redirect()->route('talabat.create')->with('success', 'Talabat items added successfully.');
        } catch (\Exception $e) {
            Log::error('Error in Talabat Store Method', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'An error occurred while adding the talabat items.'], 500);
        }
    }

    private function generateNextSerialNumber($lastItem)
    {
        if ($lastItem) {
            $lastSerialNumber = (int) substr($lastItem->serial_number, 2);
            $nextSerialNumber = 'SN' . str_pad($lastSerialNumber + 1, 5, '0', STR_PAD_LEFT);
        } else {
            $nextSerialNumber = 'SN00001';
        }

        return $nextSerialNumber;
    }
}
