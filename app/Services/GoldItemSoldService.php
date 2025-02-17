<?php

namespace App\Services;

use App\Models\GoldItemSold;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GoldItemSoldService
{
    public function getGoldItemsSold($request)
    {
        $query = GoldItemSold::query();

        $userShopName = Auth::user()->shop_name;
        
        // Filter by user's shop name
        $query->where('shop_name', $userShopName);
        // Apply search filter
        if ($search = $request->input('search')) {
            $normalizedSearch = ltrim(preg_replace('/\D/', '', $search), '0');

            $query->where(function ($query) use ($normalizedSearch) {
                $query->where('model', 'like', "%{$normalizedSearch}%")
                    ->orWhere('model', 'like', "%-" . substr($normalizedSearch, 1) . "%");
            });
        }

        // Apply filters
        if ($goldColor = $request->input('gold_color')) {
            $query->whereIn('gold_color', $goldColor);
        }

        if ($kind = $request->input('kind')) {
            $query->whereIn('kind', $kind);
        }

        if ($shopName = $request->input('shop_name')) {
            $query->whereIn('shop_name', $shopName);
        }
        if ($sold_date = $request->input('sold_date')) {
            $query->whereIn('sold_date', $sold_date);
        }

        // Define sortable fields
        $sortableFields = ['serial_number', 'model', 'kind', 'quantity', 'sold_date'];
        $sortField = in_array($request->input('sort'), $sortableFields) 
            ? $request->input('sort') 
            : 'sold_date';
        $sortDirection = $request->input('direction') === 'asc' ? 'asc' : 'desc';

        return $query->orderBy($sortField, $sortDirection)
                    ->paginate(20)
                    ->appends($request->all());
    }
}
