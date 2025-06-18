<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GoldItem;
use App\Models\GoldItemSold;
use App\Models\GoldPoundInventory;
use App\Models\SaleRequest;
use App\Services\Admin_GoldItemService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminShopController extends Controller
{
    private Admin_GoldItemService $adminGoldItemService;

    public function __construct(Admin_GoldItemService $adminGoldItemService)
    {
        $this->adminGoldItemService = $adminGoldItemService;
    }

    public function getAllItems(Request $request)
    {
        $goldItems = $this->adminGoldItemService->getGoldItems($request);
        return view('Shops.Gold.all_items', compact('goldItems'));
    }

    public function getItemDetails($serial_number)
    {
        $item = GoldItem::where('serial_number', $serial_number)->first();
        if (!$item) {
            $item = GoldItemSold::where('serial_number', $serial_number)->first();
        }
        return response()->json($item);
    }

    public function getItemsByModel(Request $request)
    {
        $model = $request->input('model');
        Log::info('Getting items with model', ['model' => $model]);

        $items = GoldItem::where('model', $model)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'serial_number' => $item->serial_number,
                    'shop_name' => $item->shop_name,
                    'weight' => $item->weight,
                    'status' => $item->status,
                    'model' => $item->model,
                    'gold_color' => $item->gold_color
                ];
            });
        
        return response()->json(['items' => $items]);
    }

    public function submitPoundPrice(Request $request)
    {
        try {
            $validated = $request->validate([
                'serial_number' => 'required|string',
                'customer_id' => 'required|exists:customers,id',
                'price' => 'required|numeric|min:0'
            ]);

            DB::transaction(function () use ($validated) {
                $poundInventory = GoldPoundInventory::where('serial_number', $validated['serial_number'])
                    ->firstOrFail();

                SaleRequest::create([
                    'item_serial_number' => $validated['serial_number'],
                    'shop_name' => Auth::user()->shop_name,
                    'status' => 'pending',
                    'customer_id' => $validated['customer_id'],
                    'price' => $validated['price'],
                    'payment_method' => 'cash',
                    'item_type' => 'pound',
                    'weight' => $poundInventory->goldPound->weight,
                    'purity' => $poundInventory->goldPound->purity,
                    'kind' => $poundInventory->goldPound->kind
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Pound price submitted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to submit pound price: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit pound price: ' . $e->getMessage()
            ], 500);
        }
    }

    public function checkAssociatedPounds(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:gold_items,id'
        ]);

        foreach ($validated['ids'] as $id) {
            $goldItem = GoldItem::findOrFail($id);
            $poundInventory = GoldPoundInventory::where('related_item_serial', $goldItem->serial_number)->first();
            
            if ($poundInventory) {
                $pound = $poundInventory->goldPound;
                return response()->json([
                    'hasPound' => true,
                    'poundDetails' => [
                        'serial_number' => $poundInventory->serial_number,
                        'kind' => $pound->kind,
                        'weight' => $pound->weight,
                        'purity' => $pound->purity
                    ]
                ]);
            }
        }

        return response()->json(['hasPound' => false]);
    }
} 