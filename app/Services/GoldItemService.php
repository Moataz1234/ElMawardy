<?php

namespace App\Services;

use App\Models\GoldItem;
use App\Models\GoldItemSold;
use App\Models\Shop;
use Illuminate\Support\Facades\Storage;

class GoldItemService
{
    
    public function createGoldItem(array $validatedData, string $imagePath = null)
    {
        // Automatically generate the next serial number
        $lastItem = GoldItem::orderByRaw('CAST(SUBSTRING(serial_number, 3) AS UNSIGNED) DESC')->first();
        $nextSerialNumber = $this->generateNextSerialNumber($lastItem);

        GoldItem::create([
            'serial_number' => $nextSerialNumber,
            'shop_id' => $validatedData['shop_id'],
            'shop_name' => Shop::find($validatedData['shop_id'])->name,
            'kind' => $validatedData['kind'],
            'model' => $validatedData['model'],
            'gold_color' => $validatedData['gold_color'],
            'metal_type' => $validatedData['metal_type'],
            'metal_purity' => $validatedData['metal_purity'],
            'quantity' => $validatedData['quantity'],
            'weight' => $validatedData['weight'],
            'source' => $validatedData['source'],
            'link' => $imagePath,
        ]);
    }

    public function updateGoldItem($id, array $validatedData, $file = null)
    {
        $goldItem = GoldItem::findOrFail($id);

        if ($file) {
            // Store the file
            $imagePath = $file->store('uploads/gold_items', 'public');
            $validatedData['link'] = $imagePath;

            // Optionally delete the old image if needed
            if ($goldItem->link) {
                Storage::delete('public/' . $goldItem->link);
            }
        }

        // Update the GoldItem
        $goldItem->update($validatedData);
    }

    private function generateNextSerialNumber($lastItem)
    {
        if ($lastItem) {
            preg_match('/(\d+)$/', $lastItem->serial_number, $matches);
            $lastNumber = $matches ? (int)$matches[0] : 0;
            return 'G-' . str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
        }

        return 'G-000001';
    }
    public function getGoldItems( $request)
{
    $query = GoldItem::with('shop');

    // Apply search filter
    if ($search = $request->input('search')) {
        // Normalize search input by removing non-numeric characters and leading zeros
        $normalizedSearch = ltrim(preg_replace('/\D/', '', $search), '0');

        $query->where(function ($query) use ($normalizedSearch) {
            $query->where('model', 'like', "%{$normalizedSearch}%")
                ->orWhere('model', 'like', "%-" . substr($normalizedSearch, 1) . "%"); // Handles "1-0010" pattern
        });
    }
    // Apply filters for metal purity and kind
    if ($metalPurity = $request->input('metal_purity')) {
        $query->whereIn('metal_purity', $metalPurity);
    }

    if ($kind = $request->input('kind')) {
        $query->whereIn('kind', $kind);
    }

    // Sort and paginate results, preserving query parameters
    return $query->orderBy($request->input('sort', 'serial_number'), $request->input('direction', 'asc'))
                 ->paginate(20)
                 ->appends($request->all());
}

    public function getWeightAnalysis()
{
    // Total weight of all gold items
    $totalGoldItemWeight = GoldItem::sum('weight');

    // Total weight of sold gold items for today
    $totalGoldItemSoldWeightToday = GoldItemSold::whereDate('sold_date', now()->toDateString())->sum('weight');

    // Perform other analysis as needed (kind, shop, etc.)
    $kindAnalysis = $this->analyzeByKind();
    $shopAnalysis = $this->analyzeByShop();
    $soldKindAnalysis = $this->analyzeSoldByKind();
    $soldShopAnalysis = $this->analyzeSoldByShop();

    return compact('totalGoldItemWeight', 'totalGoldItemSoldWeightToday', 'kindAnalysis', 'shopAnalysis', 'soldKindAnalysis', 'soldShopAnalysis');
}

private function analyzeByKind()
{
    // Example analysis for gold items by kind
    $Kinds = GoldItem::select('kind')->distinct()->get();
    $analysis = [];

    foreach ($Kinds as $Kind) {
        $count = GoldItem::where('kind', $Kind->kind)->count();
        $weight = GoldItem::where('kind', $Kind->kind)->sum('weight');
        $analysis[$Kind->kind] = [
            'count' => $count,
            'weight' => $weight
        ];
    }

    return $analysis;
}
private function analyzeByShop()
{
    $shops = GoldItem::select('shop_name')->distinct()->get();
    $analysis = [];

    foreach ($shops as $shop) {
        $count = GoldItem::where('shop_name', $shop->shop_name)->count();
        $weight = GoldItem::where('shop_name', $shop->shop_name)->sum('weight');
        $analysis[$shop->shop_name] = [
            'count' => $count,
            'weight' => $weight
        ];
    }

    return $analysis;
}
private function analyzeSoldByKind()
{
    $Kinds = GoldItemSold::select('kind')->distinct()->get();
    $analysis = [];

    foreach ($Kinds as $Kind) {
        $count = GoldItemSold::where('kind', $Kind->kind)->count();
        $weight = GoldItemSold::where('kind', $Kind->kind)->sum('weight');
        $analysis[$Kind->kind] = [
            'count' => $count,
            'weight' => $weight
        ];
    }

    return $analysis;
}
private function analyzeSoldByShop()
    {
        $shops = GoldItemSold::select('shop_name')->distinct()->get();
        $analysis = [];

        foreach ($shops as $shop) {
            $count = GoldItemSold::where('shop_name', $shop->shop_name)->count();
            $weight = GoldItemSold::where('shop_name', $shop->shop_name)->sum('weight');
            $analysis[$shop->shop_name] = [
                'count' => $count,
                'weight' => $weight
            ];
        }

        return $analysis;
    }
}
