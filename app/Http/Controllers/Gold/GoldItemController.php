<?php

namespace App\Http\Controllers\Gold;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\GoldItemRequest;
use App\Services\Admin_GoldItemService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

use Illuminate\Support\Facades\DB;
use App\Models\GoldItem;
use App\Models\Shop;
use App\Models\Models;
use App\Models\AddRequest;
use App\Models\Talabat;
use Illuminate\Http\Request as HttpRequest; // Rename the HTTP request class to avoid conflicts


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
            'direction' => $request->input('direction', 'asc')
        ]);
    }

    public function create()
    {
        $shops = Shop::all();
        $models = Models::select('model')->get(); // Get all models
        // $talabat = Talabat::select('model')->get(); // Get all talabat models
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
            'shops' => 'required|array',
            'shops.*.shop_id' => 'required|exists:shops,id',
            'shops.*.gold_color' => 'required',
            'shops.*.weight' => 'required|numeric',
        ]);
    
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
            'shops' => $validatedData['shops']
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
        $sessionItems = array_filter($sessionItems, function($item) use ($itemId) {
            return $item['id'] !== $itemId;
        });
    
        // Reset array keys and save to session
        session()->put('gold_items', array_values($sessionItems));
    
        return response()->json([
            'success' => true,
            'total_items' => count($sessionItems)
        ]);
    }
    
    
   // In GoldItemController store method, modify the success message

   public function submitAllItems()
   {
       $sessionItems = session()->get('gold_items', []);
   
       try {
           foreach ($sessionItems as $itemData) {
               foreach ($itemData['shops'] as $shopData) {
                   $nextSerialNumber = $this->goldItemService->generateNextSerialNumber();
   
                   $requestData = [
                       'serial_number' => $nextSerialNumber,
                       'model' => $itemData['model'],
                       'shop_id' => $shopData['shop_id'],
                       'shop_name' => Shop::find($shopData['shop_id'])->name,
                       'kind' => $itemData['kind'],
                       'gold_color' => $shopData['gold_color'],
                       'metal_type' => $itemData['metal_type'],
                       'metal_purity' => $itemData['metal_purity'],
                       'quantity' => $itemData['quantity'],
                       'weight' => $shopData['weight'],
                       'talab' => isset($shopData['talab']) ? $shopData['talab'] : false,
                       'status' => 'pending'
                   ];
   
   
                   // Create the request
                   AddRequest::create($requestData);
   
               // Create notification file for the specific shop
               $notification = json_encode([
                   'message' => 'طلب جديد تمت إضافته',
                   'model' => $item->model,
                   'serial_number' => $item->serial_number,
                   'shop_name' => $item->shop_name
               ]);
   
               // Ensure the storage directory exists
               $shopName = str_replace(' ', '_', $requestData['shop_name']);
               $file = storage_path("app/notifications_{$shopName}.txt");
               
               // Ensure the directory exists
               File::ensureDirectoryExists(dirname($file));
               
               // Write the notification
               File::put($file, $notification);
   
               Log::info('Notification created for shop', [
                   'shop_name' => $shopName,
                   'notification' => $notification
               ]);
           }
           session()->forget('gold_items');

           return redirect()
               ->route('gold-items.create')
               ->with('success', 'All items submitted successfully');
       }
   } catch (\Exception $e) {
        Log::error('Error submitting all items', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return redirect()
            ->back()
            ->with('error', 'Error submitting items: ' . $e->getMessage());
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
}
