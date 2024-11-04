<?php

namespace App\Http\Controllers;
use App\Models\GoldItem;

use Illuminate\Http\Request;

class GoldCatalogController extends Controller
{
    public function ThreeView(Request $request)
{
    // Get sort parameters
    [$sortColumn, $sortDirection] = $this->getSortColumnAndDirection($request->get('sort'));
    
    
    // Start a query on Gold_Catalog
    $query = GoldItem::query();
    //search
    if ($search = $request->input('search')) {
        // Normalize search input by removing non-numeric characters and leading zeros
        $normalizedSearch = ltrim(preg_replace('/\D/', '', $search), '0');

        $query->where(function ($query) use ($normalizedSearch) {
            $query->where('model', 'like', "%{$normalizedSearch}%")
                ->orWhere('model', 'like', "%-" . substr($normalizedSearch, 1) . "%"); // Handles "1-0010" pattern
        });
    }
    if ($request->filled('metal_purity')) {
        $query->whereIn('metal_purity', $request->get('metal_purity'));
    }
    if ($request->filled('gold_color')) {
        $query->whereIn('gold_color', $request->input('gold_color'));
    }

    // Apply kind filter if selected
    if ($request->filled('kind')) {
        $query->whereIn('kind', $request->get('kind'));
    }
    // Apply sorting
    $catalogItems = $query->orderBy($sortColumn, $sortDirection)->paginate(36);

    // Add query string to pagination links
    $catalogItems->appends($request->all());

    return view('Admin.Gold.ThreeInRow',  [
        'catalogItems' => $catalogItems,
    ]);
}

    public function getSortColumnAndDirection($sort)
    {
        switch (strtolower($sort)) {
            case 'serial_number':
                return ['serial_number', 'asc'];
            case 'shop_name':
                return ['shop_name', 'asc'];
            case 'model':
                return ['model', 'asc'];
            case 'quantity':
                return ['quantity', 'desc'];
            case 'kind':
                return ['kind', 'desc'];
            case 'new':
                return ['created_at', 'desc'];
            case 'old':
                return ['created_at', 'asc'];
            default:
                return ['created_at', 'desc']; // Default sorting
        }
    }
    
    public function FourView()
    {
    $catalogItems = GoldItem::paginate(36);
    return view('GoldCatalog.AdminView.FourInRow', compact('catalogItems'));
    }

}
