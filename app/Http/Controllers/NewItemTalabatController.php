<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GoldItem;
use App\Models\Shop;
use App\Models\Talabat;

class NewItemTalabatController extends Controller
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

            return redirect()->route('gold-items.create')->with('success', 'Talabat items added successfully.');
        } catch (\Exception $e) {
            // Log::error('Error in Talabat Store Method', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'An error occurred while adding the talabat items.'], 500);
        }
    }

    public function generateNextSerialNumber($lastItem)
    {
        if ($lastItem) {
            preg_match('/(\d+)$/', $lastItem->serial_number, $matches);
            $lastNumber = $matches ? (int)$matches[0] : 0;
            return 'G-' . str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
        }

        return 'G-000001';
    }
}
