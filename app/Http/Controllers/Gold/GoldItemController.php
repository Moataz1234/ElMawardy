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
                return $shop;
            }, $validatedData['shops']),
            'stars' => $modelStars
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
        
        // Log the incoming request
        Log::info('Fetching model details for:', ['model' => $model]);
        
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

        // Get model details (including source)
        $modelDetails = Models::where('model', $model)->first();
        Log::info('Found model details:', ['details' => $modelDetails]);

        // Make sure we're explicitly including the source in the response
        $response = [
            'shopData' => $goldItems,
            'modelDetails' => $modelDetails ? [
                'model' => $modelDetails->model,
                'source' => $modelDetails->source,
                'scanned_image' => $modelDetails->scanned_image,
                // ... other model details ...
            ] : null
        ];

        return response()->json($response);
    }
}
