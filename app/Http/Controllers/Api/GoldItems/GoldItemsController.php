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
    public function getItemsForWooCommerce(Request $request)
    {
        // Get available items (not sold, not deleted, in stock)
        $items = GoldItem::with(['modelCategory', 'shop'])
            ->whereNotIn('status', ['sold', 'deleted'])
            ->where('quantity', '>', 0)
            ->get();
            
        // Format the data for WooCommerce consumption
        $formattedItems = $items->map(function($item) {
            return [
                'id' => $item->id,
                'model' => $item->model,
                'sku' => $item->modelCategory ? $item->modelCategory->SKU : ('Gold Item ' . $item->model),
                'name' => $item->modelCategory ? $item->modelCategory->name : ('Gold Item ' . $item->model),
                'description' => $item->modelCategory ? $item->modelCategory->description : ('Gold item model ' . $item->model),
                'gold_color' => $item->gold_color,      
                'metal_type' => $item->metal_type,
                'metal_purity' => $item->metal_purity,
                'weight' => $item->weight,
                'quantity' => $item->quantity,
                'shop_name' => $item->shop_name,
                'shop_id' => $item->shop_id,
                'kind' => $item->kind,
                'price' => $item->weight * 1000, // Example pricing based on weight
                'image_url' => $item->modelCategory ? $item->modelCategory->scanned_image : null,
                'attributes' => [
                    [
                        'name' => 'Material',
                        'option' => $item->gold_color
                    ],
                    [
                        'name' => 'Size',
                        'option' => $item->modelCategory && $item->modelCategory->size ? $item->modelCategory->size : '50' // Get size from model category if available
                    ],
                    [
                        'name' => 'Branch',
                        'option' => $item->shop_name
                    ]
                ]
            ];
        });
        
        // Group by model for easier processing on WordPress side
        $groupedItems = $formattedItems->groupBy('model')->map(function($items) {
            $firstItem = $items->first();
            return [
                'model' => $firstItem['model'],
                'sku' => $firstItem['sku'],
                'name' => $firstItem['name'],
                'description' => $firstItem['description'],
                'image_url' => $firstItem['image_url'],
                'variations' => $items->toArray()
            ];
        });
        
        return response()->json([
            'success' => true,
            'timestamp' => now(),
            'data' => $groupedItems->values()
        ]);
    }
    /**
     * Get inventory status for specific SKUs
     */
    public function getInventoryStatus(Request $request)
    {
        $skus = $request->input('skus', []);
        
        if (empty($skus)) {
            return response()->json([
                'success' => false,
                'message' => 'No SKUs provided'
            ], 400);
        }
        
        // Extract model IDs from SKUs (assuming SKU format: model-shop_id-id)
        $modelInfo = [];
        foreach ($skus as $sku) {
            $parts = explode('-', $sku);
            if (count($parts) >= 2) {
                $modelInfo[] = [
                    'model' => $parts[0],
                    'shop_id' => $parts[1]
                ];
            }
        }
        
        // Get inventory for these models and shops
        $inventory = [];
        foreach ($modelInfo as $info) {
            $items = GoldItem::where('model', $info['model'])
                ->where('shop_id', $info['shop_id'])
                ->whereNotIn('status', ['sold', 'deleted'])
                ->select('id', 'model', 'shop_id', 'quantity', 'status')
                ->get();
                
            foreach ($items as $item) {
                $sku = $item->model . '-' . $item->shop_id . '-' . $item->id;
                $inventory[$sku] = [
                    'quantity' => $item->quantity,
                    'status' => $item->status,
                    'in_stock' => ($item->quantity > 0 && $item->status !== 'sold' && $item->status !== 'deleted')
                ];
            }
        }
        
        return response()->json([
            'success' => true,
            'timestamp' => now(),
            'data' => $inventory
        ]);
    }
    public function getGroupedItemsForWooCommerce(Request $request)
    {
        // First, get all the SKUs from OnlineModels table
        $onlineModelSkus = OnlineModel::pluck('sku')->toArray();
        
        // Get the latest gold price
        $goldPrice = GoldPrice::latest()->first();
        if (!$goldPrice) {
            return response()->json(['error' => 'Gold price not found'], 404);
        }
        
        // Get all available items (not sold, not deleted, in stock) that match online models
        $items = GoldItem::with(['modelCategory', 'shop'])
            ->whereNotIn('status', ['sold', 'deleted'])
            ->where('quantity', '>', 0)
            ->whereIn('model', $onlineModelSkus) // Only include models that match SKUs in OnlineModels
            ->get();
        
        // Group the items by model only
        $groupedItems = $items->groupBy('model');
        
        $formattedItems = [];
        
        foreach ($groupedItems as $model => $modelItems) {
            // Find the first item with a valid modelCategory for model info
            $itemWithCategory = $modelItems->first(function($item) {
                return $item->modelCategory !== null;
            }) ?? $modelItems->first();
            
            // Get all unique branches, shop_ids, and colors
            $branches = $modelItems->pluck('shop_name')->unique()->values()->toArray();
            $shopIds = $modelItems->pluck('shop_id')->unique()->values()->toArray();
            $colors = $modelItems->pluck('gold_color')->unique()->values()->toArray();
            
            // Get the largest weight among all items with this model
            $maxWeight = $modelItems->max('weight');
            
            // Calculate total quantity for this model
            $totalQuantity = $modelItems->sum('quantity');

            // Determine the price based on model stars
            $pricePerGram = 0;
            if ($itemWithCategory->modelCategory) {
                switch ($itemWithCategory->modelCategory->stars) {
                    case '***':
                        $pricePerGram = $goldPrice->gold_with_work;
                        break;
                    case '**':
                        $pricePerGram = $goldPrice->shoghl_agnaby;
                        break;
                    case '*':
                        $pricePerGram = $goldPrice->elashfoor;
                        break;
                    default:
                        $pricePerGram = $goldPrice->gold_sell; // fallback to regular gold sell price
                }
            }
            
            // Create the model item
            $modelItem = [
                'model' => $model,
                'sku' => $itemWithCategory->modelCategory ? $itemWithCategory->modelCategory->SKU : $model,
                'name' => $itemWithCategory->modelCategory ? $itemWithCategory->modelCategory->name : ('Gold Item ' . $model),
                'description' => $itemWithCategory->modelCategory ? $itemWithCategory->modelCategory->description : ('Gold item model ' . $model),
                'image_url' => $itemWithCategory->modelCategory ? $itemWithCategory->modelCategory->scanned_image : null,
                'weight' => $maxWeight,
                'price' => $maxWeight * $pricePerGram,
                'quantity' => $totalQuantity,
                'kind' => $itemWithCategory->kind,
                'metal_type' => $itemWithCategory->metal_type,
                'metal_purity' => $itemWithCategory->metal_purity,
                // 'stars' => $itemWithCategory->modelCategory ? $itemWithCategory->modelCategory->stars : null,
                // 'price_type' => $itemWithCategory->modelCategory ? $itemWithCategory->modelCategory->stars : 'regular'
            ];
            
            // Create variations grouped by unique combinations of branch, color, weight, and price
            $variations = [];
            $skuSuffix = 'A'; // Start with 'A'
            
            // Group items by unique combinations
            $uniqueCombinations = [];
            
            foreach ($modelItems as $item) {
                // Determine the price based on model stars
                $pricePerGram = 0;
                if ($item->modelCategory) {
                    switch ($item->modelCategory->stars) {
                        case '***':
                            $pricePerGram = $goldPrice->gold_with_work;
                            break;
                        case '**':
                            $pricePerGram = $goldPrice->shoghl_agnaby;
                            break;
                        case '*':
                            $pricePerGram = $goldPrice->elashfoor;
                            break;
                        default:
                            $pricePerGram = $goldPrice->gold_sell; // fallback to regular gold sell price
                    }
                }
                
                $itemPrice = $item->weight * $pricePerGram;
                
                // Create a unique key for this combination
                $combinationKey = $item->shop_name . '|' . $item->gold_color . '|' . $item->weight . '|' . $itemPrice;
                
                if (!isset($uniqueCombinations[$combinationKey])) {
                    $uniqueCombinations[$combinationKey] = [
                        'items' => [],
                        'branch' => $item->shop_name,
                        'color' => $item->gold_color,
                        'weight' => $item->weight,
                        'price' => $itemPrice,
                        'total_quantity' => 0
                    ];
                }
                
                $uniqueCombinations[$combinationKey]['items'][] = $item;
                $uniqueCombinations[$combinationKey]['total_quantity'] += $item->quantity;
            }
            
            // Create variations for each unique combination
            foreach ($uniqueCombinations as $combination) {
                // Generate SKU for this variation
                $baseSku = $itemWithCategory->modelCategory ? $itemWithCategory->modelCategory->SKU : $model;
                $variationSku = $baseSku . $skuSuffix;
                
                // Create variation
                $variations[] = [
                    'sku' => $variationSku,
                    'branch' => $combination['branch'],
                    'color' => $combination['color'],
                    'weight' => $combination['weight'],
                    'price' => $combination['price'],
                    'quantity' => $combination['total_quantity'],
                    'item_ids' => collect($combination['items'])->pluck('id')->toArray(),
                    'attributes' => [
                        [
                            'name' => 'Branch',
                            'option' => $combination['branch']
                        ],
                        [
                            'name' => 'Material',
                            'option' => $combination['color']
                        ],
                        [
                            'name' => 'Weight',
                            'option' => $combination['weight']
                        ],
                        [
                            'name' => 'Size',
                            'option' => $itemWithCategory->modelCategory && isset($itemWithCategory->modelCategory->size) ? $itemWithCategory->modelCategory->size : '50'
                        ]
                    ]
                ];
                
                // Move to next SKU suffix
                $skuSuffix++;
            }
            
            // Add this model with its variations to the result
            $modelItem['variations'] = $variations;
            $formattedItems[] = $modelItem;
        }
        
        return response()->json([
            'success' => true,
            'timestamp' => now(),
            'data' => $formattedItems
        ]);
    }
    
    public function getSimplifiedGroupedItemsForWooCommerce(Request $request)
    {
        // First, get all the SKUs from OnlineModels table
        $onlineModelSkus = OnlineModel::pluck('sku')->toArray();
        
        // Get the latest gold price
        $goldPrice = GoldPrice::latest()->first();
        if (!$goldPrice) {
            return response()->json(['error' => 'Gold price not found'], 404);
        }
        
        // Get all available items (not sold, not deleted, in stock) that match online models
        // Exclude specific shops: Rabea, other, Other
        $items = GoldItem::with(['modelCategory', 'shop'])
            ->whereNotIn('status', ['sold', 'deleted'])
            ->where('quantity', '>', 0)
            ->whereIn('model', $onlineModelSkus) // Only include models that match SKUs in OnlineModels
            ->whereNotIn('shop_name', ['Rabea', 'other', 'Other']) // Exclude specified shops
            ->get();
        
        // Group the items by model only
        $groupedItems = $items->groupBy('model');
        
        $formattedItems = [];
        
        foreach ($groupedItems as $model => $modelItems) {
            // Find the first item with a valid modelCategory for model info
            $itemWithCategory = $modelItems->first(function($item) {
                return $item->modelCategory !== null;
            }) ?? $modelItems->first();
            
            // Get all unique branches and colors
            $branches = $modelItems->pluck('shop_name')->unique()->values()->toArray();
            $colors = $modelItems->pluck('gold_color')->unique()->values()->toArray();
            
            // Calculate total quantity for this model
            $totalQuantity = $modelItems->sum('quantity');
            
            // Create the model item (simplified - removed name, description, image_url, metal_purity, metal_type)
            $modelItem = [
                'model' => $model,
                'sku' => $itemWithCategory->modelCategory ? $itemWithCategory->modelCategory->SKU : $model,
                'total_quantity' => $totalQuantity,
                'kind' => $itemWithCategory->kind,
            ];
            
            // Create variations grouped by branch (each branch gets a unique SKU suffix)
            $variations = [];
            $skuSuffix = 'A'; // Start with 'A'
            
            // Group items by unique combinations
            $uniqueCombinations = [];
            
            foreach ($modelItems as $item) {
                // Determine the price based on model stars
                $pricePerGram = 0;
                if ($item->modelCategory) {
                    switch ($item->modelCategory->stars) {
                        case '***':
                            $pricePerGram = $goldPrice->gold_with_work;
                            break;
                        case '**':
                            $pricePerGram = $goldPrice->shoghl_agnaby;
                            break;
                        case '*':
                            $pricePerGram = $goldPrice->elashfoor;
                            break;
                        default:
                            $pricePerGram = $goldPrice->gold_sell; // fallback to regular gold sell price
                    }
                }
                
                $itemPrice = $item->weight * $pricePerGram;
                
                // Create a unique key for this combination
                $combinationKey = $item->shop_name . '|' . $item->gold_color . '|' . $item->weight . '|' . $itemPrice;
                
                if (!isset($uniqueCombinations[$combinationKey])) {
                    $uniqueCombinations[$combinationKey] = [
                        'items' => [],
                        'branch' => $item->shop_name,
                        'color' => $item->gold_color,
                        'weight' => $item->weight,
                        'price' => $itemPrice,
                        'quantity' => 0
                    ];
                }
                
                $uniqueCombinations[$combinationKey]['items'][] = $item;
                $uniqueCombinations[$combinationKey]['quantity'] += $item->quantity;
            }
            
            // Create variations for each unique combination
            foreach ($uniqueCombinations as $combination) {
                // Generate SKU for this variation
                $baseSku = $itemWithCategory->modelCategory ? $itemWithCategory->modelCategory->SKU : $model;
                $variationSku = $baseSku . $skuSuffix;
                
                // Create variation
                $variations[] = [
                    'sku' => $variationSku,
                    // 'branch' => $combination['branch'],
                    // 'color' => $combination['color'],
                    // 'weight' => $combination['weight'],
                    // 'price' => $combination['price'],
                    'quantity' => $combination['quantity'],
                    // 'item_ids' => collect($combination['items'])->pluck('id')->toArray(),
                    'attributes' => [
                        [
                            'name' => 'branch',
                            'option' => $combination['branch']
                        ],
                        [
                            'name' => 'color',
                            'option' => $combination['color']
                        ],
                        [
                            'name' => 'weight',
                            'option' => $combination['weight']
                        ],
                        [
                            'name' => 'price',
                            'option' => $combination['price']
                        ],
                        // [
                        //     'name' => 'Size',
                        //     'option' => $itemWithCategory->modelCategory && isset($itemWithCategory->modelCategory->size) ? $itemWithCategory->modelCategory->size : '50'
                        // ]
                    ]
                ];
                
                // Move to next SKU suffix
                $skuSuffix++;
            }
            
            // Add this model with its variations to the result
            $modelItem['variations'] = $variations;
            $formattedItems[] = $modelItem;
        }
        
        return response()->json([
            'success' => true,
            'timestamp' => now(),
            'data' => $formattedItems
        ]);
    }
} 