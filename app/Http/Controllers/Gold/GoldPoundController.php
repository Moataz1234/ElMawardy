<?php

namespace App\Http\Controllers\Gold;

use App\Http\Controllers\Controller;
use App\Models\GoldPound;
use Illuminate\Http\Request;
use App\Models\GoldPoundInventory;
use App\Models\AddRequest;
use App\Models\SaleRequest;
use App\Services\GoldPoundService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Shop;
use App\Models\GoldItem;
use Illuminate\Support\Facades\Schema;
use App\Models\Customer;
use Illuminate\Support\Facades\Validator;
use App\Models\PoundRequest;

class GoldPoundController extends Controller
{
    protected $poundService;

    public function __construct(GoldPoundService $poundService)
    {
        $this->poundService = $poundService;
    }

    public function syncGoldPoundsInventory()
    {
        try {
            // Simple arrays of pound models
            $onePoundModels = ['5-1416', '1-1068', '5-1338-C', '2-1928', '5-1290'];
            $halfPoundModels = ['2-1899', '5-1369', '1-1291'];
            $quarterPoundModels = ['9-0194', '7-1329', '7-1013-A', '4-0854', '5-1370', '7-1386'];

            // Get all gold items that match these models
            $goldItems = GoldItem::whereIn('model', array_merge($onePoundModels, $halfPoundModels, $quarterPoundModels))->get();

            foreach ($goldItems as $item) {
                // Check if inventory record already exists
                $existingInventory = GoldPoundInventory::where('related_item_serial', $item->serial_number)->first();
                
                if ($existingInventory) {
                    continue; // Skip if record already exists
                }

                // Determine pound type and get pound details
                if (in_array($item->model, $onePoundModels)) {
                    $poundId = 1; // ID for 1 pound
                } elseif (in_array($item->model, $halfPoundModels)) {
                    $poundId = 2; // ID for 1/2 pound
                } else {
                    $poundId = 3; // ID for 1/4 pound
                }

                // Get pound details
                $goldPound = GoldPound::find($poundId);
                
                // Generate next serial number only for new records
                $serialNumber = $this->generateNextPoundSerialNumber();

                // Create new inventory record
                GoldPoundInventory::create([
                    'serial_number' => $serialNumber,
                    'related_item_serial' => $item->serial_number,
                    'gold_pound_id' => $poundId,
                    'shop_name' => $item->shop_name,
                    'type' => 'in_item',
                    'weight' => $goldPound->weight,
                    'purity' => $goldPound->purity,
                    'quantity' => 1
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Found and synced ' . $goldItems->count() . ' gold pounds'
            ]);

        } catch (\Exception $e) {
            Log::error('Error syncing pounds: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error syncing pounds: ' . $e->getMessage()
            ], 500);
        }
    }

    private function generateNextPoundSerialNumber()
    {
        $lastSerial = GoldPoundInventory::orderBy('serial_number', 'desc')
            ->where('serial_number', 'like', 'P-%')
            ->value('serial_number');

        if (!$lastSerial) {
            return 'P-0001';
        }

        $number = intval(substr($lastSerial, 2)) + 1;
        return 'P-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    public function index()
    {
        try {
            // First sync the inventory
            $syncResult = $this->syncGoldPoundsInventory();
            
            // Log the current shop name
            Log::info('Fetching inventory for shop:', ['shop_name' => Auth::user()->shop_name]);

            // Get all pound inventory items
            $shopPounds = GoldPoundInventory::with(['goldPound', 'goldItem.modelCategory'])
                ->where('shop_name', Auth::user()->shop_name)
                ->get();

            // Log the found inventory items
            Log::info('Found inventory items:', [
                'count' => $shopPounds->count(),
                'items' => $shopPounds->pluck('serial_number')->toArray()
            ]);

            return view('admin.Gold.pounds.index', compact('shopPounds'));
        } catch (\Exception $e) {
            Log::error('Error in index method:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()->with('error', 'Error loading inventory: ' . $e->getMessage());
        }
    }

    public function create()
    {
        $shops = Shop::all();
        // Get all pound types with their details
        $poundTypes = GoldPound::select('id', 'kind', 'weight', 'purity')->get();
        
        // Log the data to check what we're getting
        Log::info('Pound Types:', ['data' => $poundTypes->toArray()]);
        
        return view('admin.Gold.pounds.create', compact('shops', 'poundTypes'));
    }

    public function store(Request $request)
    {
        try {
            $goldPound = GoldPound::findOrFail($request->pound_type);
            
            $validator = Validator::make($request->all(), [
                'pound_type' => 'required|exists:gold_pounds,id',
                'quantity' => 'required|integer|min:1',
                'shop_name' => 'required|exists:shops,name',
                'serial_number' => 'required_if:type,in_item|string|nullable',
                'type' => 'required|in:standalone,in_item',
                'custom_weight' => 'required_if:kind,pound_varient,bar_varient|numeric|nullable|min:0',
                'custom_purity' => 'required_if:kind,pound_varient,bar_varient|numeric|nullable|min:0|max:999',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'فشل التحقق من البيانات',
                    'errors' => $validator->errors()->all()
                ], 422);
            }

            DB::transaction(function () use ($request, $goldPound) {
                $isVariant = in_array($goldPound->kind, ['pound_varient', 'bar_varient']);
                
                // Debug logging
                Log::info('Request data:', [
                    'custom_weight' => $request->custom_weight,
                    'is_variant' => $isVariant,
                    'pound_kind' => $goldPound->kind
                ]);

                // Set weight and purity based on type
                $weight = $isVariant ? floatval($request->custom_weight) : $goldPound->weight;
                $purity = $isVariant ? intval($request->custom_purity) : $goldPound->purity;
                
                // Handle image upload
                $imagePath = null;
                if ($request->hasFile('image')) {
                    $imagePath = $request->file('image')->store('pound-images', 'public');
                }
                
                // Create multiple requests based on quantity
                for ($i = 0; $i < $request->quantity; $i++) {
                    PoundRequest::create([
                        'serial_number' => $request->type === 'in_item' 
                            ? $request->serial_number 
                            : $this->generateNextSerialNumber(),
                        'gold_pound_id' => $goldPound->id,
                        'shop_name' => $request->shop_name,
                        'type' => $request->type,
                        'weight' => $weight,
                        'custom_weight' => $isVariant ? $weight : null,  // Store custom_weight explicitly
                        'custom_purity' => $isVariant ? $purity : null,  // Store custom_purity explicitly
                        'image_path' => $imagePath,
                        'quantity' => 1,
                        'status' => 'pending'
                    ]);
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء طلب السبيكة بنجاح'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to create pound request: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إنشاء الطلب: ' . $e->getMessage()
            ], 500);
        }
    }

    private function generateNextSerialNumber()
    {
        $lastSerial = PoundRequest::orderBy('serial_number', 'desc')
            ->value('serial_number');

        if (!$lastSerial) {
            return 'P-0001';
        }

        $number = intval(substr($lastSerial, 2)) + 1;
        return 'P-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    // Shop creates sale request
    public function createSaleRequest(Request $request)
    {
        $request->validate([
            'serial_numbers' => 'required|array',
            'serial_numbers.*' => 'required|exists:gold_pounds_inventory,serial_number',
            'prices' => 'required|array',
            'prices.*' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,visa,value,mogo,instapay',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'phone_number' => 'nullable|string',
            'address' => 'nullable|string',
            'email' => 'nullable|email'
        ]);

        try {
            DB::transaction(function () use ($request) {
                // Create or find customer
                $customer = Customer::firstOrCreate(
                    [
                        'phone_number' => $request->phone_number,
                        'email' => $request->email
                    ],
                    [
                        'first_name' => $request->first_name,
                        'last_name' => $request->last_name,
                        'address' => $request->address
                    ]
                );

                foreach ($request->serial_numbers as $serialNumber) {
                    // Get the pound inventory record with its gold pound details
                    $poundInventory = GoldPoundInventory::with('goldPound')
                        ->where('serial_number', $serialNumber)
                        ->where('shop_name', Auth::user()->shop_name)
                        ->firstOrFail();

                    // Create sale request with pound details
                    SaleRequest::create([
                        'item_serial_number' => $serialNumber,
                        'shop_name' => Auth::user()->shop_name,
                        'price' => $request->prices[$serialNumber],
                        'payment_method' => $request->payment_method,
                        'customer_id' => $customer->id,
                        'status' => 'pending',
                        'item_type' => 'pound',
                        'weight' => $poundInventory->goldPound->weight,
                        'purity' => $poundInventory->goldPound->purity,
                        'kind' => $poundInventory->goldPound->kind
                    ]);

                    // Update only the pound inventory status
                    $poundInventory->update([
                        'status' => 'pending_sale'
                    ]);
                }
            });

            return response()->json(['success' => true, 'message' => 'Sale requests created successfully']);
        } catch (\Exception $e) {
            Log::error('Failed to create sale requests: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Failed to create sale requests: ' . $e->getMessage()
            ], 500);
        }
    }

    public function showSellForm(Request $request)
    {
        $selectedPounds = $request->input('selected_pounds', []);
        
        // Filter out any null values
        $selectedPounds = array_filter($selectedPounds, function($value) {
            return !is_null($value) && $value !== '';
        });

        if (empty($selectedPounds)) {
            return redirect()->route('gold-pounds.index')
                ->with('error', 'No valid pounds selected for sale.');
        }

        // Get pounds with all related data
        $pounds = GoldPoundInventory::with([
                'goldPound',
                'goldItem.modelCategory'
            ])
            ->whereIn('serial_number', $selectedPounds)
            ->where('shop_name', Auth::user()->shop_name)
            ->get();

        Log::info('Fetched pound details:', [
            'count' => $pounds->count(),
            'data' => $pounds->toArray()
        ]);

        if ($pounds->isEmpty()) {
            return redirect()->route('gold-pounds.index')
                ->with('error', 'No matching pounds found in inventory.');
        }

        return view('admin.gold.pounds.sell_form', compact('pounds'));
    }

    public function sell(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'serial_numbers' => 'required|array',
            'serial_numbers.*' => 'exists:gold_pounds_inventory,serial_number',
            'prices' => 'required|array',
            'prices.*' => 'required|numeric|min:0'
        ]);

        foreach ($validated['serial_numbers'] as $serialNumber) {
            SaleRequest::create([
                'item_serial_number' => $serialNumber,
                'shop_name' => Auth::user()->shop_name,
                'status' => 'pending',
                'customer_id' => $validated['customer_id'],
                'price' => $validated['prices'][$serialNumber],
                'item_type' => 'pound'
            ]);

            // Update inventory status
            GoldPoundInventory::where('serial_number', $serialNumber)
                ->update(['status' => 'pending_sale']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Pound sale requests created successfully'
        ]);
    }
}

