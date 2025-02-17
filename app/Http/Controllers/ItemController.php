<?php

namespace App\Http\Controllers;

use App\Models\SaleRequest;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function getItemDetails($serial)
    {
        $saleRequest = SaleRequest::where('item_serial_number', $serial)->first();
        
        if ($saleRequest && $saleRequest->item_type === 'pound') {
            return response()->json([
                'serial_number' => $saleRequest->item_serial_number,
                'kind' => $saleRequest->kind,
                'weight' => $saleRequest->weight,
                'purity' => $saleRequest->purity,
                'item_type' => 'pound'
            ]);
        }
        
        // Existing code for regular items...
    }
} 