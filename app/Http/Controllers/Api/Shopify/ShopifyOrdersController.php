<?php

namespace App\Http\Controllers\Api\Shopify;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ShopifyService;
use Illuminate\Support\Collection;
use App\Models\GoldItem;
use App\Models\GoldItemSold;
use App\Models\Models;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class ShopifyOrdersController extends Controller
{
    protected $shopifyService;

    public function __construct(ShopifyService $shopifyService)
    {
        $this->shopifyService = $shopifyService;
    }

    /**
     * Get paginated Shopify orders with optional filtering
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getOrders(Request $request)
    {
        // Get query parameters with defaults
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 10);
        $status = $request->input('status', 'any'); // 'unfulfilled', 'fulfilled', 'any'
        $sortDirection = $request->input('sort_direction', 'desc'); // 'asc' or 'desc'
        
        try {
            // Get orders from Shopify service - always get all orders
            $orders = collect($this->shopifyService->getOrders('any'));
            
            // Filter orders if needed
            if ($status === 'unfulfilled') {
                $orders = $orders->where('fulfillment_status', '!=', 'fulfilled');
            } elseif ($status === 'fulfilled') {
                $orders = $orders->where('fulfillment_status', 'fulfilled')
                                 ->where('financial_status', '!=', 'voided');
            }
            
            // Sort orders by created_at date
            if ($sortDirection === 'asc') {
                $orders = $orders->sortBy('created_at');
            } else {
                $orders = $orders->sortByDesc('created_at');
            }
            
            // Calculate pagination
            $total = $orders->count();
            $lastPage = ceil($total / $perPage);
            
            // Get current page items
            $currentPageItems = $orders->forPage($page, $perPage)->values();
            
            // Format orders to include only necessary data
            $formattedOrders = $currentPageItems->map(function($order) {
                return [
                    'id' => $order['id'],
                    'order_number' => $order['name'],
                    'created_at' => $order['created_at'],
                    'customer' => [
                        'name' => $order['customer']['first_name'] . ' ' . $order['customer']['last_name'],
                        'email' => $order['customer']['email'] ?? null,
                        'phone' => $order['customer']['phone'] ?? null,
                    ],
                    'shipping_address' => $order['shipping_address'] ?? null,
                    'billing_address' => $order['billing_address'] ?? null,
                    'total_price' => $order['total_price'],
                    'financial_status' => $order['financial_status'],
                    'fulfillment_status' => $order['fulfillment_status'] ?? 'unfulfilled',
                    'line_items' => collect($order['line_items'])->map(function($item) {
                        return [
                            'id' => $item['id'],
                            'title' => $item['title'],
                            'quantity' => $item['quantity'],
                            'price' => $item['price'],
                            'sku' => $item['sku'] ?? null,
                            'image_url' => $item['image_url'] ?? null,
                        ];
                    })->toArray(),
                ];
            });
            
            // Return paginated response
            return response()->json([
                'data' => $formattedOrders,
                'meta' => [
                    'current_page' => (int)$page,
                    'from' => ($page - 1) * $perPage + 1,
                    'last_page' => $lastPage,
                    'per_page' => (int)$perPage,
                    'to' => min($page * $perPage, $total),
                    'total' => $total,
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve orders',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get a specific order by ID
     * 
     * @param string $orderId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getOrder($orderId)
    {
        try {
            $order = $this->shopifyService->getOrder($orderId);
            
            if (!$order) {
                return response()->json([
                    'error' => 'Order not found'
                ], 404);
            }
            
            return response()->json([
                'data' => $order
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve order',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Assign a gold item to a Shopify order (mark as sold)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function assignItem(Request $request)
    {
        Log::info('Assign item request received', $request->all());
        
        try {
            // Validate request
            $validated = $request->validate([
                'order_id' => 'nullable',
                'item_id' => 'nullable',
                'serial_number' => 'nullable',
                'item_title' => 'nullable',
                'item_price' => 'nullable|numeric'
            ]);
            
            Log::info('Validation passed', $validated);
            
            // Find the gold item
            $goldItem = GoldItem::where('serial_number', $validated['serial_number'])->first();
            
            if (!$goldItem) {
                Log::error('Gold item not found', ['serial_number' => $validated['serial_number']]);
                return response()->json(['error' => 'Gold item not found'], 404);
            }
            
            Log::info('Gold item found', ['item' => $goldItem->toArray()]);
            
            // Get stars and source from Models table
            $modelData = Models::where('model', $goldItem->model)->first();
            $stars = null;
            $source = null;
            
            if ($modelData) {
                $stars = $modelData->stars;
                $source = $modelData->source;
                Log::info('Model data found', ['stars' => $stars, 'source' => $source]);
            } else {
                Log::warning('Model data not found for model', ['model' => $goldItem->model]);
            }
            
            // Begin transaction
            DB::beginTransaction();
            
            try {
                // Create a sold item record
                $soldItem = new GoldItemSold();
                $soldItem->serial_number = $goldItem->serial_number;
                $soldItem->shop_name = 'Online'; // Set shop name to Online
                $soldItem->shop_id = null;
                $soldItem->kind = $goldItem->kind;
                $soldItem->model = $goldItem->model;
                $soldItem->talab = $goldItem->talab;
                $soldItem->gold_color = $goldItem->gold_color;
                $soldItem->stones = $goldItem->stones;
                $soldItem->metal_type = $goldItem->metal_type;
                $soldItem->metal_purity = $goldItem->metal_purity;
                $soldItem->quantity = 1;
                $soldItem->weight = $goldItem->weight;
                $soldItem->add_date = $goldItem->created_at;
                $soldItem->price = $validated['item_price'];
                $soldItem->sold_date = now();
                $soldItem->stars = $stars; // Set stars from Models table
                $soldItem->source = $source ?? 'Shopify'; // Set source from Models table or default to 'Shopify'
                $soldItem->save();
                
                Log::info('Sold item created', ['sold_item' => $soldItem->toArray()]);
                
                // Delete the gold item from the GoldItems table
                $goldItem->delete();
                
                Log::info('Gold item deleted');
                
                // Commit transaction
                DB::commit();
                
                Log::info('Transaction committed');
                
                return response()->json([
                    'success' => true,
                    'message' => 'Item assigned successfully',
                    'sold_item_id' => $soldItem->id
                ]);
            } catch (\Exception $e) {
                // Rollback transaction
                DB::rollBack();
                
                Log::error('Error in transaction', [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                return response()->json([
                    'error' => 'Failed to assign item',
                    'message' => $e->getMessage()
                ], 500);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed', ['errors' => $e->errors()]);
            
            return response()->json([
                'error' => 'Validation failed',
                'message' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Unexpected error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'An unexpected error occurred',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
