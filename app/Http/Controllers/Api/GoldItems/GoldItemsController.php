<?php

namespace App\Http\Controllers\Api\GoldItems;

use App\Http\Controllers\Controller;
use App\Models\GoldItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Models;
use App\Models\GoldPrice;
use Illuminate\Support\Facades\Log;
use App\Models\OnlineModel;


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

    /**
     * Find gold items matching a SKU from the Models table
     */
    public function matchBySku($sku)
    {
        // First find the model with this SKU
        $model = Models::where('SKU', $sku)->first();
        
        if (!$model) {
            return response()->json(['items' => []]);
        }
        
        // Find gold items with this model that are available (not sold)
        $items = GoldItem::where('model', $model->model)
                        ->where('status', '!=', 'sold')
                        ->where('status', '!=', 'deleted')
                        ->get(['serial_number', 'model', 'weight', 'shop_name']);
        
        return response()->json(['items' => $items]);
    }

    /**
     * Get matched models with grouped shop data and prices
     */
    public function getMatchedModels(Request $request)
    {
        try {
            // Get the latest gold price
            $goldPrice = GoldPrice::latest()->first();
            if (!$goldPrice) {
                return response()->json(['error' => 'Gold price not found'], 404);
            }

            // Check if we want all items without pagination
            $getAllItems = $request->input('all', false);

            // Get SKUs from the OnlineModel table
            $onlineSkus = OnlineModel::pluck('sku')->toArray();

            if (empty($onlineSkus)) {
                return response()->json([
                    'items' => [],
                    'message' => 'No online models found',
                    'gold_price' => $goldPrice->gold_buy
                ]);
            }

            // Find models that match the online SKUs
            $matchedModels = Models::whereIn('SKU', $onlineSkus)->pluck('model')->toArray();

            if (empty($matchedModels)) {
                return response()->json([
                    'items' => [],
                    'message' => 'No matching models found for online SKUs',
                    'gold_price' => $goldPrice->gold_buy
                ]);
            }

            // Query builder for gold items
            $query = GoldItem::whereIn('model', $matchedModels)
                ->whereNotIn('status', ['sold', 'deleted'])
                ->with('modelCategory');

            // Optional model filter
            $modelFilter = $request->input('model', null);
            if ($modelFilter) {
                $query->where('model', 'like', '%' . $modelFilter . '%');
            }

            if ($getAllItems) {
                // Get all models without pagination
                $goldItems = $query->get();
                
                // Group items by model and SKU
                $groupedItems = $goldItems->groupBy('model')->map(function ($items) use ($goldPrice) {
                    $firstItem = $items->first();
                    $model = $firstItem->modelCategory;
                    $maxWeight = $items->max('weight');
                    
                    if (!$model) {
                        Log::warning('Model not found for item', [
                            'model' => $firstItem->model,
                            'item_id' => $firstItem->id
                        ]);
                    }
                    
                    return [
                        'model' => $firstItem->model,
                        'sku' => $model ? $model->SKU : null,
                        'shop_names' => $items->pluck('shop_name')->unique()->values()->toArray(),
                        'quantity' => $items->sum('quantity'),
                        'weight' => $maxWeight, // Get the biggest weight
                        'price' => $maxWeight * $goldPrice->gold_buy // Calculate price based on biggest weight
                    ];
                })->values();
                
                return response()->json([
                    'items' => $groupedItems,
                    'gold_price' => $goldPrice->gold_buy
                ]);
                
            } else {
                // Set pagination parameters
                $page = $request->input('page', 1);
                $perPage = $request->input('per_page', 20);

                // Get total count for pagination
                $totalCount = $query->distinct('model')->count('model');

                // Get models for pagination
                $models = $query->select('model')
                    ->distinct()
                    ->orderBy('model')
                    ->skip(($page - 1) * $perPage)
                    ->take($perPage)
                    ->pluck('model')
                    ->toArray();

                if (empty($models)) {
                    return response()->json([
                        'items' => [],
                        'pagination' => [
                            'total' => $totalCount,
                            'per_page' => $perPage,
                            'current_page' => $page,
                            'last_page' => ceil($totalCount / $perPage)
                        ],
                        'gold_price' => $goldPrice->gold_buy
                    ]);
                }

                // Get all items belonging to these models
                $goldItems = GoldItem::whereIn('model', $models)
                    ->whereNotIn('status', ['sold', 'deleted'])
                    ->with('modelCategory')
                    ->get();

                // Group items by model and SKU
                $groupedItems = $goldItems->groupBy('model')->map(function ($items) use ($goldPrice) {
                    $firstItem = $items->first();
                    $model = $firstItem->modelCategory;
                    $maxWeight = $items->max('weight');
                    
                    if (!$model) {
                        Log::warning('Model not found for item', [
                            'model' => $firstItem->model,
                            'item_id' => $firstItem->id
                        ]);
                    }
                    
                    return [
                        'model' => $firstItem->model,
                        'sku' => $model ? $model->SKU : null,
                        'shop_names' => $items->pluck('shop_name')->unique()->values()->toArray(),
                        'quantity' => $items->sum('quantity'),
                        'weight' => $maxWeight, // Get the biggest weight
                        'price' => $maxWeight * $goldPrice->gold_buy // Calculate price based on biggest weight
                    ];
                })->values();

                return response()->json([
                    'items' => $groupedItems,
                    'pagination' => [
                        'total' => $totalCount,
                        'per_page' => $perPage,
                        'current_page' => $page,
                        'last_page' => ceil($totalCount / $perPage)
                    ],
                    'gold_price' => $goldPrice->gold_buy
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Error in getMatchedModels: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Internal server error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get matched models formatted for WordPress WooCommerce
     */
    public function getWooCommerceProducts(Request $request)
    {
        try {
            // Get the latest gold price
            $goldPrice = GoldPrice::latest()->first();
            if (!$goldPrice) {
                return response()->json(['error' => 'Gold price not found'], 404);
            }

            // Set pagination parameters
            $page = $request->input('page', 1);
            $perPage = $request->input('per_page', 10);
            $modelFilter = $request->input('model', null);

            // Query builder for gold items
            $query = GoldItem::whereNotIn('status', ['sold', 'deleted'])
                ->with('modelCategory');

            // Apply model filter if provided
            if ($modelFilter) {
                $query->where('model', 'like', '%' . $modelFilter . '%');
            }

            // Get total count for pagination
            $totalCount = $query->count();

            // Get models for pagination
            $models = $query->select('model')
                ->distinct()
                ->orderBy('model')
                ->skip(($page - 1) * $perPage)
                ->take($perPage)
                ->pluck('model')
                ->toArray();

            if (empty($models)) {
                return response()->json([
                    'products' => [],
                    'pagination' => [
                        'total' => $totalCount,
                        'per_page' => $perPage,
                        'current_page' => $page,
                        'last_page' => ceil($totalCount / $perPage)
                    ]
                ]);
            }

            // Get all items belonging to these models
            $goldItems = GoldItem::whereIn('model', $models)
                ->whereNotIn('status', ['sold', 'deleted'])
                ->with('modelCategory')
                ->get();

            // Group items by model/SKU
            $products = [];
            $groupedByModel = $goldItems->groupBy('model');

            foreach ($groupedByModel as $model => $items) {
                $firstItem = $items->first();
                $modelInfo = $firstItem->modelCategory;

                if (!$modelInfo) {
                    Log::warning('Model not found for item', [
                        'model' => $model,
                        'item_id' => $firstItem->id
                    ]);
                    continue;
                }

                // Base product data
                $product = [
                    'name' => $model,
                    'type' => 'variable',
                    'status' => 'publish',
                    'description' => 'Gold item model: ' . $model,
                    'sku' => $modelInfo->SKU,
                    'manage_stock' => false,
                    'categories' => [
                        ['id' => 27] // Default category - adjust as needed
                    ],
                    'attributes' => [
                        [
                            'name' => 'Gold Color',
                            'variation' => true,
                            'visible' => true,
                            'options' => $items->pluck('gold_color')->unique()->filter()->values()->toArray()
                        ],
                        [
                            'name' => 'Branch',
                            'variation' => true,
                            'visible' => true,
                            'options' => $items->pluck('shop_name')->unique()->filter()->values()->toArray()
                        ]
                    ],
                    'images' => [],
                    'variations' => []
                ];

                // Add variations
                foreach ($items as $index => $item) {
                    $variation = [
                        'sku' => $modelInfo->SKU . '-' . ($index + 1),
                        'regular_price' => (string) round($item->weight * $goldPrice->gold_sell),
                        'manage_stock' => true,
                        'stock_quantity' => $item->quantity,
                        'stock_status' => 'instock',
                        'attributes' => [
                            [
                                'name' => 'Gold Color',
                                'option' => $item->gold_color ?? 'Default'
                            ],
                            [
                                'name' => 'Branch',
                                'option' => $item->shop_name
                            ]
                        ]
                    ];
                    
                    $product['variations'][] = $variation;
                }

                $products[] = $product;
            }

            return response()->json([
                'products' => $products,
                'pagination' => [
                    'total' => $totalCount,
                    'per_page' => $perPage,
                    'current_page' => $page,
                    'last_page' => ceil($totalCount / $perPage)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error in getWooCommerceProducts: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Internal server error: ' . $e->getMessage()], 500);
        }
    }
} 