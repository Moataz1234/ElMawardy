<?php

namespace App\Http\Controllers\Gold;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\GoldItemRequest;
use App\Services\Admin_GoldItemService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use App\Models\GoldItem;
use App\Models\Shop;
use App\Models\Models;
use App\Models\AddRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class GoldItemController extends Controller
{
    protected $goldItemService;

    public function __construct(Admin_GoldItemService $goldItemService)
    {
        $this->goldItemService = $goldItemService;
    }

    public function index(Request $request)
    {
        $goldItems = $this->goldItemService->getGoldItems($request);

        return view('Inventory_list', [
            'goldItems' => $goldItems,
            'search' => $request->input('search'),
            'sort' => $request->input('sort', 'serial_number'),
            'direction' => $request->input('direction', 'desc')
        ]);
    }

    public function create()
    {
        $shops = Shop::all();
        $models = Models::select('model')->get();
        $goldColors = GoldItem::select('gold_color')->distinct()->pluck('gold_color');
        $metalTypes = GoldItem::select('metal_type')->distinct()->pluck('metal_type');
        $metalPurities = GoldItem::select('metal_purity')->distinct()->pluck('metal_purity');
        $kinds = GoldItem::select('kind')->distinct()->pluck('kind');
        
        // Get sources from both GoldItem and Models tables, combine and sort them
        $goldItemSources = GoldItem::select('source')
            ->distinct()
            ->whereNotNull('source')
            ->pluck('source');
        
        $modelSources = Models::select('source')
            ->distinct()
            ->whereNotNull('source')
            ->pluck('source');
        
        // Combine sources, remove duplicates, sort, and remove empty values
        $sources = $goldItemSources->concat($modelSources)
            ->unique()
            ->filter()
            ->sort()
            ->values();
        
        return view('admin.Gold.items.Create_form', compact(
            'shops',
            'models',
            'goldColors',
            'metalTypes',
            'metalPurities',
            'kinds',
            'sources'
        ));
    }

    public function addItemToSession(Request $request)
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'model' => 'required',
            'kind' => 'required',
            'metal_type' => 'required',
            'metal_purity' => 'required',
            'quantity' => 'required|integer',
            'rest_since' => 'required|date',
            'shops' => 'required|array',
            'shops.*.shop_id' => 'required|exists:shops,id',
            'shops.*.shop_name' => 'required',
            'shops.*.gold_color' => 'required',
            'shops.*.weight' => 'required|numeric',
            'shops.*.source' => 'nullable|string',
            'shops.*.talab' => 'nullable|boolean',
        ]);

        // Get the stars and default source from the Models table
        $modelDetails = Models::where('model', $validatedData['model'])->first();
        $modelStars = $modelDetails ? $modelDetails->stars : null;
        $defaultSource = $modelDetails ? $modelDetails->source : null;

        // Get current session items or initialize empty array
        $sessionItems = session()->get('gold_items', []);

        // Generate a unique identifier for this item
        $itemId = uniqid();

        // Prepare the item data
        $itemData = [
            'id' => $itemId,
            'model' => $validatedData['model'],
            'kind' => $validatedData['kind'],
            'metal_type' => $validatedData['metal_type'],
            'metal_purity' => $validatedData['metal_purity'],
            'quantity' => $validatedData['quantity'],
            'rest_since' => $validatedData['rest_since'],
            'shops' => array_map(function($shop) use ($defaultSource) {
                // Use provided source or fall back to default source
                $shop['source'] = !empty($shop['source']) ? $shop['source'] : $defaultSource;
                $shop['talab'] = isset($shop['talab']) ? (bool)$shop['talab'] : false;
                return $shop;
            }, $validatedData['shops']),
            'stars' => $modelStars,
        ];

        // Add the item to session
        $sessionItems[] = $itemData;
        session()->put('gold_items', $sessionItems);

        // Return the added item to update the frontend table
        return response()->json([
            'success' => true,
            'item' => $itemData,
            'total_items' => count($sessionItems)
        ]);
    }

    public function removeSessionItem(Request $request)
    {
        $itemId = $request->input('id');
        $sessionItems = session()->get('gold_items', []);

        // Remove the specific item
        $sessionItems = array_filter($sessionItems, function ($item) use ($itemId) {
            return $item['id'] !== $itemId;
        });

        // Reset array keys and save to session
        session()->put('gold_items', array_values($sessionItems));

        return response()->json([
            'success' => true,
            'total_items' => count($sessionItems)
        ]);
    }

    public function submitAllItems()
    {
        Log::info('Starting submitAllItems process');
        
        $sessionItems = session()->get('gold_items', []);
        Log::info('Session items retrieved', ['count' => count($sessionItems), 'items' => $sessionItems]);
    
        if (empty($sessionItems)) {
            Log::warning('No items found in session');
            return response()->json([
                'success' => false,
                'message' => 'No items to submit'
            ]);
        }
    
        try {
            DB::beginTransaction();
            Log::info('Database transaction started');
    
            foreach ($sessionItems as $itemData) {
                Log::info('Processing item', ['item' => $itemData]);
                
                foreach ($itemData['shops'] as $shopData) {
                    Log::info('Processing shop data', ['shop' => $shopData]);
                    
                    try {
                        $nextSerialNumber = $this->goldItemService->generateNextSerialNumber();
                        Log::info('Generated serial number', ['serial' => $nextSerialNumber]);
    
                        $shop = Shop::find($shopData['shop_id']);
                        Log::info('Found shop', ['shop_id' => $shopData['shop_id'], 'shop_name' => $shop ? $shop->name : 'not found']);
    
                        $requestData = [
                            'serial_number' => $nextSerialNumber,
                            'model' => $itemData['model'],
                            'shop_id' => $shopData['shop_id'],
                            'shop_name' => $shop ? $shop->name : null,
                            'kind' => $itemData['kind'],
                            'gold_color' => $shopData['gold_color'],
                            'metal_type' => $itemData['metal_type'],
                            'metal_purity' => $itemData['metal_purity'],
                            'quantity' => $itemData['quantity'],
                            'weight' => $shopData['weight'],
                            'talab' => isset($shopData['talab']) ? $shopData['talab'] : false,
                            'status' => 'pending',
                            'rest_since' => $itemData['rest_since'] ?? now()->toDateString(),
                            'source' => $shopData['source'] ?? null,
                        ];
                        
                        Log::info('Prepared request data', ['requestData' => $requestData]);
    
                        // Create the request
                        $item = AddRequest::create($requestData);
                        Log::info('Created add request', ['item_id' => $item->id]);

                        // Check if talab is false and update for_production table
                        if (!$requestData['talab']) {
                            $productionOrder = \App\Models\ForProduction::where('model', $itemData['model'])
                                ->where('gold_color', $shopData['gold_color'])
                                ->first();
                            if ($productionOrder && $productionOrder->not_finished > 0) {
                                $productionOrder->decrement('not_finished', $itemData['quantity']);
                                Log::info('Updated production order', [
                                    'model' => $itemData['model'],
                                    'gold_color' => $shopData['gold_color'],
                                    'decreased_by' => $itemData['quantity'],
                                    'remaining_not_finished' => $productionOrder->fresh()->not_finished
                                ]);
                            } else {
                                Log::info('No matching production order found or already completed', [
                                    'model' => $itemData['model'],
                                    'gold_color' => $shopData['gold_color']
                                ]);
                            }
                        }
    
                        // Create notification
                        $notification = json_encode([
                            'message' => 'طلب جديد تمت إضافته',
                            'model' => $item->model,
                            'serial_number' => $item->serial_number,
                            'shop_name' => $item->shop_name
                        ]);
    
                        $shopName = str_replace(' ', '_', $requestData['shop_name']);
                        $file = storage_path("app/notifications_{$shopName}.txt");
                        
                        Log::info('Creating notification file', ['path' => $file]);
                        
                        // Ensure the directory exists
                        File::ensureDirectoryExists(dirname($file));
                        File::put($file, $notification);
                        
                        Log::info('Notification file created successfully');
    
                    } catch (\Exception $e) {
                        Log::error('Error processing shop data', [
                            'error' => $e->getMessage(),
                            'shop_data' => $shopData,
                            'trace' => $e->getTraceAsString()
                        ]);
                        throw $e;
                    }
                }
            }
    
            // Clear the session
            session()->forget('gold_items');
            Log::info('Session cleared successfully');
            
            DB::commit();
            Log::info('Database transaction committed successfully');
    
            return response()->json([
                'success' => true,
                'message' => 'All items submitted successfully'
            ]);
    
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in submitAllItems', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
    
            return response()->json([
                'success' => false,
                'message' => 'Error submitting items: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit(string $id)
    {
        $goldItem = GoldItem::findOrFail($id);
        
        // Get all unique kinds from GoldItem table
        $kinds = GoldItem::select('kind')->distinct()->pluck('kind');
        
        // Get all unique shop names from GoldItem table
        $shopNames = GoldItem::select('shop_name')->distinct()->pluck('shop_name');
        
        return view('admin.Gold.Items.Edit_form', compact('goldItem', 'kinds', 'shopNames'));
    }
    public function update(Request $request, $id)
    {
        $goldItem = GoldItem::findOrFail($id);
        
        // Validate the request data
        $validatedData = $request->validate([
            'shop_name' => 'required',
            'shop_id' => 'required|numeric',
            'kind' => 'required|string',
            'model' => 'required|string',
            'talab' => 'required|boolean',
            'gold_color' => 'required',
            'stones' => 'nullable',
            'metal_type' => 'required',
            'metal_purity' => 'required',
            'quantity' => 'required|numeric',
            'weight' => 'required|numeric',
            'rest_since' => 'required|date',
        ]);
        
        // If the "other" option was selected, use the custom kind value
        if ($request->has('custom_kind') && !empty($request->custom_kind)) {
            $validatedData['kind'] = $request->custom_kind;
        }
        
        // If the "other" option was selected for shop name, use the custom shop name
        if ($request->has('custom_shop') && !empty($request->custom_shop)) {
            $validatedData['shop_name'] = $request->custom_shop;
        }
        
        // Convert talab to boolean
        $validatedData['talab'] = (bool)$request->talab;
        
        try {
            // Update the gold item
            $goldItem->update($validatedData);
            
            return redirect()->route('admin.inventory')->with('success', 'Gold item updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update gold item: ' . $e->getMessage());
        }
    }
    public function checkExists($model)
    {
        $exists = Models::where('model', $model)->exists();
        return response()->json(['exists' => $exists]);
    }

    public function getModelDetails(Request $request)
    {
        $model = $request->query('model');
        
        Log::info('Fetching model details for:', ['model' => $model]);
        
        try {
            // For pending requests
            $pendingRequests = AddRequest::where('model', $model)
                ->where('status', 'pending')
                ->get()
                ->map(function ($request) {
                    return [
                        'shop_id' => $request->shop_id,
                        'shop_name' => $request->shop_name,
                        'gold_color' => $request->gold_color,
                        'total_weight' => $request->weight,
                        'weights' => [$request->weight], // Individual weight
                        'count' => $request->quantity,
                        'serial_numbers' => [$request->serial_number],
                        'source' => $request->source,
                        'is_pending' => true
                    ];
                });

            // For gold items
            $goldItems = GoldItem::where('model', $model)
                ->whereNotIn('status', ['sold', 'deleted'])
                ->get()
                ->groupBy(function($item) {
                    return $item->shop_name . '_' . $item->gold_color;
                })
                ->map(function ($group) {
                    $firstItem = $group->first();
                    return [
                        'shop_id' => $firstItem->shop_id,
                        'shop_name' => $firstItem->shop_name,
                        'gold_color' => $firstItem->gold_color,
                        'total_weight' => $group->sum('weight'),
                        'weights' => $group->pluck('weight')->toArray(), // Individual weights
                        'count' => $group->sum('quantity'),
                        'serial_numbers' => $group->pluck('serial_number')->toArray(),
                        'source' => $firstItem->source,
                        'is_pending' => false
                    ];
                })
                ->values()
                ->sortBy([
                    ['count', 'asc'],
                    ['shop_id', 'asc']
                ])
                ->values();

            // Get model details
            $modelDetails = Models::where('model', $model)->first();

            // Combine pending requests and existing items
            $allItems = $pendingRequests->concat($goldItems);

            Log::info('Mapped gold items:', [
                'pendingRequests' => $pendingRequests->toArray(),
                'goldItems' => $goldItems->toArray()
            ]);

            return response()->json([
                'shopData' => $allItems,
                'modelDetails' => $modelDetails ? [
                    'model' => $modelDetails->model,
                    'source' => $modelDetails->source,
                    'scanned_image' => $modelDetails->scanned_image,
                ] : null
            ]);

        } catch (\Exception $e) {
            Log::error('Error in getModelDetails:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Error fetching model details'], 500);
        }
    }

    
}
