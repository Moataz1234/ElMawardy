<?php

namespace App\Services;

use App\Models\GoldPoundSold;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GoldPoundSoldService
{
    public function getGoldPoundsSold($request)
    {
        $query = GoldPoundSold::query()
            ->with(['goldPound', 'customer']); // Eager load relationships

        $userShopName = Auth::user()->shop_name;
        
        // Filter by user's shop name
        $query->where('shop_name', $userShopName);

        // Apply search filter if provided
        if ($search = $request->input('search')) {
            $query->where('serial_number', 'like', "%{$search}%");
        }

        // Apply shop name filter if provided
        if ($shopName = $request->input('shop_name')) {
            $query->whereIn('shop_name', $shopName);
        }

        // Define sortable fields
        $sortableFields = ['serial_number', 'price', 'created_at'];
        $sortField = in_array($request->input('sort'), $sortableFields) 
            ? $request->input('sort') 
            : 'created_at';
        $sortDirection = $request->input('direction') === 'asc' ? 'asc' : 'desc';

        return $query->orderBy($sortField, $sortDirection)
                    ->paginate(20)
                    ->appends($request->all());
    }
} 