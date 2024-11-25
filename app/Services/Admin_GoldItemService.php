<?php

namespace App\Services;

use App\Models\GoldItem;
use App\Models\GoldItemSold;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Services\SortAndFilterService;
// use App\Services\PriceCalculator;

class Admin_GoldItemService
{

    protected $sortAndFilterService;

    public function __construct(SortAndFilterService $sortAndFilterService)
    {
        $this->sortAndFilterService = $sortAndFilterService;

    }
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
    public function findGoldItem($id)
    {
        return GoldItem::findOrFail($id);
    }

    public function updateGoldItem(Request $request, $id)
    {
        $goldItem = $this->findGoldItem($id);
        $goldItem->update($request->validated());
        return $goldItem;
    }

    public function bulkDelete(array $ids)
    {
        return GoldItem::whereIn('id', $ids)->delete();
    }

    public function bulkRequest(array $ids)
    {
        // Implement your request logic here
        return GoldItem::whereIn('id', $ids)
            ->update(['status' => 'requested']);
    }
    // public function updateGoldItem($id, array $validatedData, $file = null)
    // {
    //     $goldItem = GoldItem::findOrFail($id);

    //     if ($file) {
    //         // Store the file
    //         $imagePath = $file->store('uploads/gold_items', 'public');
    //         $validatedData['link'] = $imagePath;

    //         // Optionally delete the old image if needed
    //         if ($goldItem->link) {
    //             Storage::delete('public/' . $goldItem->link);
    //         }
    //     }

    //     // Update the GoldItem
    //     $goldItem->update($validatedData);
    // }

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
    $query = GoldItem::query();

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
    if ($category = $request->input('category')) {
        $query->whereHas('modelCategory', function ($q) use ($category) {
            $q->whereIn('category', $category);
        });
    }
    if ($kind = $request->input('shop_name')) {
        $query->whereIn('shop_name', $kind);
    }
    $sortableFields = ['serial_number', 'model', 'kind', 'quantity', 'created_at'];
    $sortField = in_array($request->input('sort'), $sortableFields) ? $request->input('sort') : 'serial_number';
    $sortDirection = $request->input('direction') === 'desc' ? 'desc' : 'asc';

    // Step 4: Return paginated, sorted, and filtered results
    return $query->orderBy($sortField, $sortDirection)
                 ->paginate(20)
                 ->appends($request->all());


//     $query = GoldItem::query();
//     $allowedFilters = [
//     'search',
//     'metal_purity',
//     'gold_color',
//     'kind',
// ];

// $goldItems = $this->sortAndFilterService->getFilteredAndSortedResults(
//     $query,
//     $request,
//     $allowedFilters
// );
// $gold_color = $query->distinct('gold_color')->pluck('gold_color')->toArray();
// $kind = $query->distinct('kind')->pluck('kind')->toArray();

// return [
//     'goldItems' => $goldItems,
//     'totalPages' => $goldItems->lastPage() ,
//     'gold_color' => $gold_color,
//     'kind' => $kind,
// ];
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
