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
        return view('admin.Gold.items.Create_form', compact(
            'shops',
            'models',
            'goldColors',
            'metalTypes',
            'metalPurities',
            'kinds'
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
        ]);

        // Get the stars from the Models table
        $modelStars = Models::where('model', $validatedData['model'])->value('stars');

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
            'shops' => $validatedData['shops'],
            'stars' => $modelStars // Add the stars value
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
                        ];
                        
                        Log::info('Prepared request data', ['requestData' => $requestData]);
    
                        // Create the request
                        $item = AddRequest::create($requestData);
                        Log::info('Created add request', ['item_id' => $item->id]);
    
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
        $shops = Shop::all();

        return view('admin.Gold.Items.Edit_form', compact('goldItem', 'shops'));
    }

    public function checkExists($model)
    {
        $exists = Models::where('model', $model)->exists();
        return response()->json(['exists' => $exists]);
    }

    public function getModelDetails(Request $request)
    {
        $model = $request->query('model');
        
        // Get existing gold items
        $goldItems = GoldItem::where('model', $model)
            ->whereNotIn('status', ['sold', 'deleted'])
            ->get()
            ->map(function ($item) {
                return [
                    'shop_id' => $item->shop_id,
                    'shop_name' => $item->shop_name,
                    'gold_color' => $item->gold_color,
                    'total_weight' => $item->weight,
                    'count' => $item->quantity,
                    'serial_numbers' => [$item->serial_number],
                    'is_pending' => false
                ];
            });

        // Get pending add requests
        $pendingRequests = AddRequest::where('model', $model)
            ->where('status', 'pending')
            ->get()
            ->map(function ($request) {
                return [
                    'shop_id' => $request->shop_id,
                    'shop_name' => $request->shop_name,
                    'gold_color' => $request->gold_color,
                    'total_weight' => $request->weight,
                    'count' => $request->quantity,
                    'serial_numbers' => [$request->serial_number],
                    'is_pending' => true
                ];
            });

        // Group existing items by shop and color
        $groupedGoldItems = $goldItems->groupBy(function ($item) {
            return $item['shop_id'] . '-' . $item['gold_color'];
        })->map(function ($group) {
            $firstItem = $group->first();
            return [
                'shop_id' => $firstItem['shop_id'],
                'shop_name' => $firstItem['shop_name'],
                'gold_color' => $firstItem['gold_color'],
                'total_weight' => $group->sum('total_weight'),
                'count' => $group->sum('count'),
                'serial_numbers' => $group->pluck('serial_numbers')->flatten()->toArray(),
                'is_pending' => false
            ];
        });

        // Group pending requests by shop and color
        $groupedPendingRequests = $pendingRequests->groupBy(function ($item) {
            return $item['shop_id'] . '-' . $item['gold_color'];
        })->map(function ($group) {
            $firstItem = $group->first();
            return [
                'shop_id' => $firstItem['shop_id'],
                'shop_name' => $firstItem['shop_name'],
                'gold_color' => $firstItem['gold_color'],
                'total_weight' => $group->sum('total_weight'),
                'count' => $group->sum('count'),
                'serial_numbers' => $group->pluck('serial_numbers')->flatten()->toArray(),
                'is_pending' => true
            ];
        });

        // Combine and sort the data
        $allData = $groupedGoldItems->concat($groupedPendingRequests);
        
        // Sort the data according to requirements
        $sortedData = $allData->sortBy([
            // First by pending status (non-pending items first)
            ['is_pending', 'asc'],
            // Then by count (lower counts first)
            ['count', 'asc'],
            // Then by shop_id (lower IDs first)
            ['shop_id', 'asc']
        ])->values();

        // Get model details (including image)
        $modelDetails = Models::where('model', $model)->first();

        return response()->json([
            'shopData' => $sortedData,
            'modelDetails' => $modelDetails
        ]);
    }
}
