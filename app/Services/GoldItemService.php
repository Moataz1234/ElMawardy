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
    public function getGoldItems($request)
    {
        $search = $request->input('search');
        $sort = $request->input('sort', 'serial_number');
        $direction = $request->input('direction', 'asc');

        return GoldItem::with('shop')
            ->when($search, function ($query, $search) {
                return $query->where('serial_number', 'like', "%{$search}%")
                    ->orWhereHas('shop', function ($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%");
                    })
                    ->orWhere('kind', 'like', "%{$search}%")
                    ->orWhere(function ($query) use ($search) {
                        $baseModel = preg_replace('/-[A-D]$/', '', $search);
                        $query->where('model', 'like', "%{$baseModel}%");
                    })
                    ->orWhere('gold_color', 'like', "%{$search}%")
                    ->orWhere('stones', 'like', "%{$search}%")
                    ->orWhere('metal_type', 'like', "%{$search}%")
                    ->orWhere('metal_purity', 'like', "%{$search}%")
                    ->orWhere('source', 'like', "%{$search}%");
            })
            ->orderBy($sort, $direction)
            ->paginate(20);
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
