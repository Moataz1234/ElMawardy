<?php

namespace App\Http\Controllers\Api\GoldItems;

use App\Http\Controllers\Controller;
use App\Models\GoldItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GoldItemsController extends Controller
{
    /**
     * Display a listing of gold items.
     */
    public function index()
    {
        $goldItems = GoldItem::all();
        return response()->json($goldItems);
    }

    /**
     * Store a newly created gold item.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'model' => 'required|string',
            'serial_number' => 'required|string|unique:gold_items',
            'kind' => 'nullable|string',
            'shop_name' => 'nullable|string|exists:users,shop_name',
            'shop_id' => 'nullable|string',
            'weight' => 'nullable|numeric',
            'gold_color' => 'nullable|string',
            'metal_type' => 'nullable|string',
            'metal_purity' => 'nullable|string',
            'quantity' => 'integer|default:1',
            'stones' => 'nullable|string',
            'talab' => 'nullable|boolean',
            'status' => 'string|default:available',
            'rest_since' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $goldItem = GoldItem::create($request->all());
            return response()->json($goldItem, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create gold item'], 500);
        }
    }

    /**
     * Display the specified gold item.
     */
    public function show($id)
    {
        try {
            $goldItem = GoldItem::with(['user', 'modelCategory'])->findOrFail($id);
            return response()->json($goldItem);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gold item not found'], 404);
        }
    }

    /**
     * Update the specified gold item.
     */
    public function update(Request $request, $id)
    {
        try {
            $goldItem = GoldItem::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'model' => 'string',
                'serial_number' => 'string|unique:gold_items,serial_number,' . $id,
                'kind' => 'nullable|string',
                'shop_name' => 'nullable|string|exists:users,shop_name',
                'shop_id' => 'nullable|string',
                'weight' => 'nullable|numeric',
                'gold_color' => 'nullable|string',
                'metal_type' => 'nullable|string',
                'metal_purity' => 'nullable|string',
                'quantity' => 'integer',
                'stones' => 'nullable|string',
                'talab' => 'nullable|boolean',
                'status' => 'string',
                'rest_since' => 'nullable|date',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $goldItem->update($request->all());
            return response()->json($goldItem);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gold item not found'], 404);
        }
    }

    /**
     * Remove the specified gold item.
     */
    public function destroy($id)
    {
        try {
            $goldItem = GoldItem::findOrFail($id);
            $goldItem->delete();
            return response()->json(null, 204);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gold item not found'], 404);
        }
    }
} 